<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Charger les paramètres de l'établissement
        $schoolSettings = \App\Models\SchoolSettings::getSettings();
        
        $query = Student::query();
        
        // Recherche textuelle
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if ($request->has('cycle') && $request->cycle) {
            $query->whereHas('enrollments.schoolClass.levelData', function($q) use ($request) {
                $q->where('cycle', $request->cycle);
            });
        }
        
        if ($request->has('level') && $request->level) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('level_id', $request->level);
            });
        }
        
        if ($request->has('class') && $request->class) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('id', $request->class);
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $students = $query->with([
                              'enrollments.schoolClass.levelData', 
                              'enrollments.schoolClass', 
                              'parents'
                          ])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
        
        // Données pour les filtres
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->active()->get();
        
        // Statistiques
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $newThisMonth = Student::whereMonth('enrollment_date', now()->month)
                             ->whereYear('enrollment_date', now()->year)
                             ->count();
        
        // Statistiques par cycle
        $studentsByCycle = [
            'preprimaire' => Student::whereHas('enrollments.schoolClass.levelData', function($q) {
                $q->where('cycle', 'preprimaire');
            })->count(),
            'primaire' => Student::whereHas('enrollments.schoolClass.levelData', function($q) {
                $q->where('cycle', 'primaire');
            })->count(),
            'college' => Student::whereHas('enrollments.schoolClass.levelData', function($q) {
                $q->where('cycle', 'college');
            })->count(),
            'lycee' => Student::whereHas('enrollments.schoolClass.levelData', function($q) {
                $q->where('cycle', 'lycee');
            })->count(),
        ];
        
        return view('students.index', compact(
            'students', 
            'levels', 
            'classes',
            'totalStudents',
            'activeStudents', 
            'newThisMonth',
            'studentsByCycle',
            'schoolSettings'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->where('is_active', true)->get();
        $academicYears = AcademicYear::where('status', 'active')->get();
        
        return view('students.create', compact('levels', 'classes', 'academicYears'));
    }

    /**
     * Get student details for modal view (AJAX)
     */
    public function getStudentDetails($id)
    {
        $student = Student::with([
            'parents',
            'enrollments' => function($query) {
                $query->where('status', 'active')->with(['schoolClass.level', 'academicYear']);
            }
        ])->findOrFail($id);
        
        // Forcer le rechargement des relations
        $student->refresh();
        $student->load([
            'enrollments' => function($query) {
                $query->where('status', 'active')->with(['schoolClass.level', 'academicYear']);
            }
        ]);

        // Récupérer l'inscription active
        $activeEnrollment = $student->enrollments->first();
        
        // Debug logging
        Log::info('Student details request', [
            'student_id' => $id,
            'enrollments_count' => $student->enrollments->count(),
            'active_enrollment' => $activeEnrollment ? 'exists' : 'null',
            'class_name' => $activeEnrollment ? $activeEnrollment->schoolClass->name ?? 'null' : 'no_enrollment',
            'level_name' => $activeEnrollment && $activeEnrollment->schoolClass ? $activeEnrollment->schoolClass->level->name ?? 'null' : 'no_class'
        ]);

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'date_of_birth' => $student->date_of_birth->format('d/m/Y'),
                'age' => $student->age,
                'gender' => $student->gender,
                'place_of_birth' => $student->place_of_birth,
                'address' => $student->address,
                'emergency_contact' => $student->emergency_contact,
                'medical_conditions' => $student->medical_conditions,
                'enrollment_date' => $student->enrollment_date->format('d/m/Y'),
                'status' => $student->status,
                'photo' => $student->photo ? asset('storage/' . $student->photo) : null,
                'parents' => $student->parents->map(function($parent) {
                    return [
                        'first_name' => $parent->first_name,
                        'last_name' => $parent->last_name,
                        'phone' => $parent->phone,
                        'email' => $parent->email,
                        'address' => $parent->address,
                        'profession' => $parent->profession
                    ];
                }),
                'current_enrollment' => $activeEnrollment ? [
                    'class_name' => $activeEnrollment->schoolClass->name ?? null,
                    'level_name' => $activeEnrollment->schoolClass->level->name ?? null,
                    'academic_year' => $activeEnrollment->academicYear->name ?? null
                ] : null
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log des données reçues
        Log::info('Store method called', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('photo'),
            'create_enrollment' => $request->input('create_enrollment')
        ]);
        
        $validated = $request->validate([
            // Le matricule est toujours généré automatiquement, pas de validation nécessaire
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'place_of_birth' => 'nullable|string|max:255',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string',
            'enrollment_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:active,inactive,graduated,transferred',
            
            // Informations pour l'inscription optionnelle
            'create_enrollment' => 'nullable|string|in:on',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'class_id' => 'nullable|exists:classes,id',
            

        ]);

        // Générer automatiquement le matricule - toujours obligatoire
        $validated['student_id'] = Student::generateStudentId();

        // Définir le statut par défaut
        $validated['status'] = $validated['status'] ?? 'active';

        // Handle photo upload avec débogage
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if ($file->isValid()) {
                $validated['photo'] = $file->store('students/photos', 'public');
                Log::info('Photo uploadée avec succès: ' . $validated['photo']);
            } else {
                Log::error('Fichier photo invalide');
            }
        } else {
            Log::info('Aucun fichier photo reçu');
        }

        DB::beginTransaction();
        try {
            // Créer l'élève
            $student = Student::create($validated);

            // Créer l'inscription si demandée (checkbox cochée = "on")
            if ($validated['create_enrollment'] === 'on' && !empty($validated['academic_year_id']) && !empty($validated['class_id'])) {
                $enrollmentData = [
                    'student_id' => $student->id,
                    'academic_year_id' => $validated['academic_year_id'],
                    'class_id' => $validated['class_id'],
                    'enrollment_date' => $validated['enrollment_date'],
                    'enrollment_status' => 'active',
                    'status' => 'active',
                    'is_new_enrollment' => true,
                ];

                \App\Models\Enrollment::create($enrollmentData);
            }

            DB::commit();

            $successMessage = 'Élève ajouté avec succès!' . 
                           ($validated['create_enrollment'] === 'on' ? ' Inscription créée.' : '') .
                           ' Matricule généré: ' . $student->student_id;

            Log::info('Élève créé avec succès', [
                'student_id' => $student->id,
                'matricule' => $student->student_id,
                'name' => $student->full_name
            ]);

            // Si c'est une requête AJAX, retourner JSON
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'student' => [
                        'id' => $student->id,
                        'student_id' => $student->student_id,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'full_name' => $student->full_name,
                        'date_of_birth' => $student->date_of_birth->format('d/m/Y'),
                        'age' => $student->age,
                        'gender' => $student->gender,
                        'enrollment_created' => $validated['create_enrollment'] === 'on'
                    ]
                ]);
            }

            return redirect()->route('students.index')->with('success', $successMessage);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création de l\'élève', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Si c'est une requête AJAX, retourner JSON
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création de l\'élève: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la création de l\'élève: ' . $e->getMessage()]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = SchoolClass::where('is_active', true)->get();
        $academicYears = AcademicYear::where('status', 'active')->get();
        
        return view('students.edit', compact('student', 'classes', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'place_of_birth' => 'nullable|string|max:255',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle photo upload avec débogage
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                if ($file->isValid()) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                        Storage::disk('public')->delete($student->photo);
                        Log::info('Ancienne photo supprimée: ' . $student->photo);
                    }
                    $validated['photo'] = $file->store('students/photos', 'public');
                    Log::info('Nouvelle photo uploadée: ' . $validated['photo']);
                } else {
                    Log::error('Fichier photo invalide lors de la modification');
                }
            } else {
                Log::info('Aucun fichier photo reçu lors de la modification');
            }

            $student->update($validated);

            // Retourner avec un message de succès
            return redirect()->route('students.index')->with('success', 'Élève modifié avec succès!');
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner avec un message d'erreur
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la modification : ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            // Supprimer la photo associée si elle existe
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            
            // Supprimer l'étudiant (les relations seront supprimées automatiquement grâce aux contraintes de clé étrangère)
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Élève supprimé avec succès!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search students
     */
    public function search(Request $request)
    {
        $query = Student::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Recherche par paramètre 'q' pour la recherche par matricule (API pour parents)
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('class') && $request->class) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('name', $request->class);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pour l'API de recherche parent, limiter les résultats et les champs
        if ($request->has('q')) {
            $students = $query->active()
                             ->select('id', 'student_id', 'first_name', 'last_name', 'date_of_birth')
                             ->orderBy('student_id')
                             ->limit(10)
                             ->get();
        } else {
            $students = $query->with(['enrollments.schoolClass', 'parents'])->get();
        }

        return response()->json($students);
    }

    /**
     * Get classes by level for AJAX
     */
    public function getClassesByLevel(Request $request)
    {
        $levelId = $request->level_id;
        $classes = SchoolClass::where('is_active', true)
            ->where('level_id', $levelId)
            ->orderBy('name')
            ->get(['id', 'name', 'capacity']);
        
        return response()->json($classes);
    }

    /**
     * Get classes by cycle for AJAX
     */
    public function getClassesByCycle(Request $request)
    {
        $cycle = $request->cycle;
        $classes = SchoolClass::where('is_active', true)
            ->whereHas('level', function($query) use ($cycle) {
                $query->where('cycle', $cycle);
            })
            ->with('level')
            ->get();
        
        return response()->json($classes);
    }

    /**
     * Vérifier le matricule d'un élève pour la réinscription
     */
    public function checkMatricule(Request $request)
    {
        $matricule = $request->input('matricule');
        
        if (!$matricule) {
            return response()->json([
                'success' => false,
                'message' => 'Matricule requis'
            ], 400);
        }

        // Rechercher l'élève par matricule
        $student = Student::where('student_id', $matricule)->first();
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Matricule non trouvé'
            ]);
        }

        // Vérifier si l'élève est déjà inscrit pour l'année scolaire en cours
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        
        if ($currentAcademicYear) {
            $currentEnrollment = $student->enrollments()
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('status', 'active')
                ->with(['schoolClass.level'])
                ->first();
                
            if ($currentEnrollment) {
                return response()->json([
                    'success' => true,
                    'already_enrolled' => true,
                    'student' => $student,
                    'current_class' => $currentEnrollment->schoolClass->name ?? 'N/A',
                    'current_level' => $currentEnrollment->schoolClass->level->name ?? 'N/A'
                ]);
            }
        }

        // Obtenir la dernière classe de l'élève
        $lastEnrollment = $student->enrollments()
            ->with(['schoolClass.level'])
            ->orderBy('created_at', 'desc')
            ->first();
            
        $lastClass = $lastEnrollment ? $lastEnrollment->schoolClass->name : 'Aucune';

        return response()->json([
            'success' => true,
            'already_enrolled' => false,
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'date_of_birth' => $student->date_of_birth->format('Y-m-d'),
                'gender' => $student->gender
            ],
            'last_class' => $lastClass
        ]);
    }
}
