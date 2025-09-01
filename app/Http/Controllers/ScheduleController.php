<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Afficher la liste des emplois du temps par classe
     */
    public function index(Request $request)
    {
        // Récupérer l'année académique courante ou celle sélectionnée
        $academicYearId = $request->get('academic_year_id');
        if (!$academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $academicYearId = $currentYear?->id;
        }

        // Récupérer les classes qui ont des emplois du temps
        $classesWithSchedules = SchoolClass::active()
            ->with(['level', 'schedules' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }])
            ->whereHas('schedules', function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->orderBy('name')
            ->paginate(15);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::find($academicYearId);

        return view('schedules.index', compact('classesWithSchedules', 'academicYears', 'currentAcademicYear'));
    }

    /**
     * Afficher le formulaire de création d'emploi du temps (sélection de classe)
     */
    public function create()
    {
        $classes = SchoolClass::active()->with('level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        return view('schedules.create', compact('classes', 'academicYears', 'currentAcademicYear'));
    }

    /**
     * Afficher le formulaire de constitution d'emploi du temps pour une classe
     */
    public function build(Request $request)
    {
        $classId = $request->get('class_id');
        $academicYearId = $request->get('academic_year_id');

        if (!$classId || !$academicYearId) {
            return redirect()->route('schedules.create')->with('error', 'Veuillez sélectionner une classe et une année académique.');
        }

        $class = SchoolClass::with('level')->findOrFail($classId);
        $academicYear = AcademicYear::findOrFail($academicYearId);
        
        // Récupérer les matières du niveau de la classe
        $subjects = Subject::active()->where('level_id', $class->level_id)->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('first_name')->get();
        
        // Récupérer les enseignants par matière pour l'utilisation côté client
        $teachersBySubject = [];
        foreach ($subjects as $subject) {
            $teachersBySubject[$subject->id] = $subject->teachers()->active()->get(['teachers.id', 'teachers.first_name', 'teachers.last_name']);
        }

        // Récupérer l'emploi du temps existant s'il y en a un
        $existingSchedules = Schedule::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Créneaux horaires par défaut
        $defaultTimeSlots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '10:15'], // Récréation
            ['start' => '10:15', 'end' => '11:15'],
            ['start' => '11:15', 'end' => '12:15'],
            ['start' => '12:15', 'end' => '13:15'], // Pause déjeuner
            ['start' => '13:15', 'end' => '14:15'],
            ['start' => '14:15', 'end' => '15:15'],
            ['start' => '15:15', 'end' => '15:30'], // Récréation
            ['start' => '15:30', 'end' => '16:30'],
        ];

        // Jours de la semaine
        $days = [
            1 => 'Lundi',
            2 => 'Mardi', 
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi'
        ];

        return view('schedules.build', compact(
            'class', 'academicYear', 'subjects', 'teachers', 'teachersBySubject',
            'existingSchedules', 'defaultTimeSlots', 'days'
        ));
    }

    /**
     * Enregistrer l'emploi du temps d'une classe
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'schedule_data' => 'required|array',
            'schedule_data.*.day' => 'required|integer|between:1,6',
            'schedule_data.*.start_time' => 'required|date_format:H:i',
            'schedule_data.*.end_time' => 'required|date_format:H:i|after:schedule_data.*.start_time',
            'schedule_data.*.type' => 'required|in:course,break',
            'schedule_data.*.subject_id' => 'nullable|exists:subjects,id',
            'schedule_data.*.teacher_id' => 'nullable|exists:teachers,id',
            'schedule_data.*.room' => 'nullable|string|max:100',
            'schedule_data.*.title' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // Supprimer l'emploi du temps existant pour cette classe et cette année
            Schedule::where('class_id', $validated['class_id'])
                   ->where('academic_year_id', $validated['academic_year_id'])
                   ->delete();

            $createdSchedules = 0;
            $errors = [];

            foreach ($validated['schedule_data'] as $scheduleItem) {
                // Ignorer les créneaux vides
                if ($scheduleItem['type'] === 'course' && 
                    (empty($scheduleItem['subject_id']) || empty($scheduleItem['teacher_id']))) {
                    continue;
                }

                // Vérifier les conflits d'enseignants pour les cours
                if ($scheduleItem['type'] === 'course' && !empty($scheduleItem['teacher_id'])) {
                    $conflicts = Schedule::validateTimeSlot(
                        $validated['class_id'],
                        $scheduleItem['teacher_id'],
                        $validated['academic_year_id'],
                        $scheduleItem['day'],
                        $scheduleItem['start_time'],
                        $scheduleItem['end_time']
                    );

                    if ($conflicts['teacher_conflict']) {
                        $dayNames = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        $errors[] = "Conflit enseignant le {$dayNames[$scheduleItem['day']]} de {$scheduleItem['start_time']} à {$scheduleItem['end_time']}";
                        continue;
                    }
                }

                // Créer l'enregistrement
                $scheduleData = [
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'day_of_week' => $scheduleItem['day'],
                    'start_time' => $scheduleItem['start_time'],
                    'end_time' => $scheduleItem['end_time'],
                    'type' => $scheduleItem['type'],
                    'is_active' => true,
                ];

                if ($scheduleItem['type'] === 'course') {
                    $scheduleData['subject_id'] = $scheduleItem['subject_id'];
                    $scheduleData['teacher_id'] = $scheduleItem['teacher_id'];
                    $scheduleData['room'] = $scheduleItem['room'] ?? null;
                } else {
                    $scheduleData['title'] = $scheduleItem['title'] ?? 'Pause';
                }

                Schedule::create($scheduleData);
                $createdSchedules++;
            }

            DB::commit();

            $message = "Emploi du temps enregistré avec succès! {$createdSchedules} créneaux créés.";
            if (!empty($errors)) {
                $message .= " Avertissements: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'schedules_created' => $createdSchedules,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails de l'emploi du temps d'une classe
     */
    public function show(SchoolClass $class, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        if (!$academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $academicYearId = $currentYear?->id;
        }

        $class->load('level');
        $academicYear = AcademicYear::find($academicYearId);

        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Organiser les horaires par jour
        $schedulesByDay = $schedules->groupBy('day_of_week');
        
        $days = [
            1 => 'Lundi',
            2 => 'Mardi', 
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi'
        ];

        return view('schedules.show', compact('class', 'academicYear', 'schedulesByDay', 'days'));
    }

    /**
     * Afficher le formulaire d'édition d'emploi du temps
     */
    public function edit(SchoolClass $class, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        if (!$academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $academicYearId = $currentYear?->id;
        }

        return redirect()->route('schedules.build', [
            'class_id' => $class->id,
            'academic_year_id' => $academicYearId
        ]);
    }

    /**
     * Supprimer l'emploi du temps d'une classe
     */
    public function destroy(SchoolClass $class, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        if (!$academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $academicYearId = $currentYear?->id;
        }

        $deletedCount = Schedule::where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Emploi du temps supprimé avec succès! {$deletedCount} créneaux supprimés."
        ]);
    }

    /**
     * API: Obtenir les matières par niveau
     */
    public function getSubjectsByLevel(Request $request)
    {
        $levelId = $request->get('level_id');
        
        $subjects = Subject::active()
            ->where('level_id', $levelId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }

    /**
     * API: Vérifier les conflits d'horaires
     */
    public function checkConflicts(Request $request)
    {
        $conflicts = Schedule::validateTimeSlot(
            $request->class_id,
            $request->teacher_id,
            $request->academic_year_id,
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $request->exclude_id
        );

        return response()->json($conflicts);
    }

    /**
     * Afficher la version imprimable de l'emploi du temps d'une classe
     */
    public function print(SchoolClass $class, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        if (!$academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $academicYearId = $currentYear?->id;
        }

        $class->load('level');
        $academicYear = AcademicYear::find($academicYearId);

        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Organiser les horaires par jour
        $schedulesByDay = $schedules->groupBy('day_of_week');
        
        $days = [
            1 => 'Lundi',
            2 => 'Mardi', 
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi'
        ];

        return view('schedules.print', compact('class', 'academicYear', 'schedulesByDay', 'days'));
    }
}