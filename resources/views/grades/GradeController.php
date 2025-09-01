<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $examType = $request->get('exam_type');
        $term = $request->get('term');
        $teacherId = $request->get('teacher_id');

        // Construire la requête avec les filtres
        $query = Grade::with(['student', 'subject', 'schoolClass', 'teacher']);

        if ($classId) {
            $query->byClass($classId);
        }
        if ($subjectId) {
            $query->bySubject($subjectId);
        }
        if ($examType) {
            $query->byExamType($examType);
        }
        if ($term) {
            $query->byTerm($term);
        }
        if ($teacherId) {
            $query->byTeacher($teacherId);
        }

        $grades = $query->orderBy('exam_date', 'desc')->paginate(20);

        // Statistiques
        $stats = $this->getGradeStats($request);

        // Données pour les filtres et le modal
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::orderBy('first_name')->get();
        $students = Student::orderBy('first_name')->get();

        return view('grades.index', compact(
            'grades',
            'stats',
            'classes',
            'subjects',
            'teachers',
            'students'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $teachers = Teacher::orderBy('first_name')->get();

        return view('grades.create', compact('students', 'subjects', 'classes', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'exam_type' => 'required|in:devoir,composition,controle,oral',
            'term' => 'required|in:1er trimestre,2ème trimestre,3ème trimestre',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'exam_date' => 'required|date',
            'comments' => 'nullable|string|max:500'
        ]);

        // Vérifier que la note ne dépasse pas le maximum
        if ($validated['score'] > $validated['max_score']) {
            return back()->withErrors(['score' => 'La note ne peut pas dépasser le maximum.'])->withInput();
        }

        // Récupérer l'année académique active
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            return back()->withErrors(['error' => 'Aucune année académique active trouvée.'])->withInput();
        }

        // Ajouter l'année académique aux données validées
        $validated['academic_year_id'] = $academicYear->id;

        $grade = Grade::create($validated);

        // Charger les relations pour une réponse JSON complète
        $grade->load(['student', 'subject', 'schoolClass', 'teacher']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Note enregistrée avec succès !',
                'grade' => $grade,
            ], 201);
        }

        return redirect()->route('grades.index')
            ->with('success', 'Note enregistrée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $grade = Grade::with(['student', 'subject', 'schoolClass', 'teacher'])->findOrFail($id);
        
        return view('grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $grade = Grade::findOrFail($id);
        $students = Student::orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $teachers = Teacher::orderBy('first_name')->get();

        return view('grades.edit', compact('grade', 'students', 'subjects', 'classes', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'exam_type' => 'required|in:devoir,composition,controle,oral',
            'term' => 'required|in:1er trimestre,2ème trimestre,3ème trimestre',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'exam_date' => 'required|date',
            'comments' => 'nullable|string|max:500'
        ]);

        // Vérifier que la note ne dépasse pas le maximum
        if ($validated['score'] > $validated['max_score']) {
            return back()->withErrors(['score' => 'La note ne peut pas dépasser le maximum.'])->withInput();
        }

        $grade->update($validated);

        return redirect()->route('grades.index')
            ->with('success', 'Note mise à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Note supprimée avec succès !');
    }

    /**
     * Obtenir les statistiques des notes
     */
    private function getGradeStats(Request $request)
    {
        $query = Grade::query();

        // Appliquer les mêmes filtres que pour la liste
        if ($request->get('class_id')) {
            $query->byClass($request->get('class_id'));
        }
        if ($request->get('subject_id')) {
            $query->bySubject($request->get('subject_id'));
        }
        if ($request->get('term')) {
            $query->byTerm($request->get('term'));
        }

        $totalGrades = $query->count();
        
        if ($totalGrades > 0) {
            // Calculer la moyenne en pourcentage puis convertir sur 20
            $averageGrade = $query->avg(DB::raw('(score / max_score) * 20'));
            $pendingGrades = $this->getPendingGradesCount($request);
            $completionRate = $this->getCompletionRate($request);
        } else {
            $averageGrade = 0;
            $pendingGrades = 0;
            $completionRate = 0;
        }

        return [
            'totalGrades' => $totalGrades,
            'averageGrade' => round($averageGrade, 1),
            'pendingGrades' => $pendingGrades,
            'completionRate' => $completionRate
        ];
    }

    /**
     * Compter les notes en attente de saisie
     */
    private function getPendingGradesCount(Request $request)
    {
        // Logique pour déterminer les notes manquantes
        // Ceci est un exemple simplifié
        return 45; // À implémenter selon votre logique métier
    }

    /**
     * Calculer le taux de complétion
     */
    private function getCompletionRate(Request $request)
    {
        // Logique pour calculer le taux de complétion
        // Ceci est un exemple simplifié
        return 92; // À implémenter selon votre logique métier
    }

    /**
     * Export des notes
     */
    public function export(Request $request)
    {
        // Logique d'export à implémenter
        return response()->json(['message' => 'Export en cours de développement']);
    }

    /**
     * Notes par élève
     */
    public function byStudent(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $grades = Grade::where('student_id', $studentId)
            ->with(['subject', 'schoolClass', 'teacher'])
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('grades.by-student', compact('student', 'grades'));
    }

    /**
     * Notes par classe
     */
    public function byClass(Request $request, $classId)
    {
        $class = SchoolClass::findOrFail($classId);
        $grades = Grade::where('class_id', $classId)
            ->with(['student', 'subject', 'teacher'])
            ->orderBy('exam_date', 'desc')
            ->paginate(50);

        return view('grades.by-class', compact('class', 'grades'));
    }
}
