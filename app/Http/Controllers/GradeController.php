<?php

namespace App\Http\Controllers;

use App\Models\StudentGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $query = StudentGrade::with(['student', 'subject', 'schoolClass', 'teacher']);

        if ($classId) {
            $query->byClass($classId);
        }
        if ($subjectId) {
            $query->bySubject($subjectId);
        }
        if ($term) {
            $query->byTerm($term);
        }
        if ($teacherId) {
            $query->byTeacher($teacherId);
        }

        // Récupérer les notes groupées par élève
        $gradesByStudent = $query->with(['student.enrollments.schoolClass', 'subject', 'teacher'])
            ->get()
            ->groupBy('student_id');

        // Calculer les moyennes cumulées pour chaque élève
        $studentsWithGrades = [];
        foreach ($gradesByStudent as $studentId => $studentGrades) {
            $student = $studentGrades->first()->student;
            $classId = $studentGrades->first()->class_id;
            
            // Calculer la moyenne cumulée de l'élève dans cette classe
            $cumulativeGrades = StudentGrade::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->get();
            
            if ($cumulativeGrades->count() > 0) {
                $totalScore = $cumulativeGrades->sum('score');
                $totalMaxScore = $cumulativeGrades->sum('max_score');
                $cumulativeScore = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 20, 2) : 0;
                $cumulativePercentage = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 100, 1) : 0;
                
                // Déterminer la couleur de la note
                $cumulativeGradeColor = 'danger';
                if ($cumulativePercentage >= 80) {
                    $cumulativeGradeColor = 'success';
                } elseif ($cumulativePercentage >= 70) {
                    $cumulativeGradeColor = 'info';
                } elseif ($cumulativePercentage >= 60) {
                    $cumulativeGradeColor = 'primary';
                } elseif ($cumulativePercentage >= 50) {
                    $cumulativeGradeColor = 'warning';
                } elseif ($cumulativePercentage >= 40) {
                    $cumulativeGradeColor = 'secondary';
                }
            } else {
                $cumulativeScore = '--';
                $cumulativePercentage = 0;
                $cumulativeGradeColor = 'secondary';
            }
            
            $studentsWithGrades[] = [
                'student' => $student,
                'grades' => $studentGrades,
                'cumulative_score' => $cumulativeScore,
                'cumulative_percentage' => $cumulativePercentage,
                'cumulative_grade_color' => $cumulativeGradeColor,
                'total_grades' => $studentGrades->count(),
                'class' => $studentGrades->first()->schoolClass
            ];
        }

        // Pagination manuelle pour les élèves
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedStudents = array_slice($studentsWithGrades, $offset, $perPage);
        
        // Créer un objet de pagination personnalisé
        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedStudents,
            count($studentsWithGrades),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Statistiques
        $stats = $this->getGradeStats($request);

        // Données pour les filtres
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
    public function create(Request $request)
    {
        // Récupérer les niveaux pour la sélection hiérarchique
        $levels = \App\Models\Level::active()->orderBy('order')->get();
        
        // Si un student_id est fourni, récupérer l'élève
        $selectedStudent = null;
        if ($request->has('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        return view('grades.create', compact('levels', 'selectedStudent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log pour debug
        Log::info('Store method appelée', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'has_grades' => $request->has('grades'),
            'grades_is_array' => is_array($request->input('grades')),
            'grades_count' => is_array($request->input('grades')) ? count($request->input('grades')) : 0,
            'request_data' => $request->all()
        ]);

        // Vérifier si c'est un envoi multiple (nouveau format)
        if ($request->has('grades') && is_array($request->input('grades'))) {
            return $this->storeMultipleGrades($request);
        }
        
        // Ancien format pour une seule note
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
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

        // Récupérer la classe de l'élève et le professeur de la matière
        $student = Student::with(['enrollments.schoolClass'])->findOrFail($validated['student_id']);
        $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
        
        if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
            return back()->withErrors(['error' => 'L\'élève n\'est pas inscrit dans une classe active.'])->withInput();
        }

        // Récupérer le professeur qui enseigne cette matière dans cette classe
        $teacher = Teacher::whereHas('subjects', function ($query) use ($validated) {
            $query->where('subjects.id', $validated['subject_id']);
        })->whereHas('classes', function ($query) use ($currentEnrollment) {
            $query->where('classes.id', $currentEnrollment->class_id);
        })->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Aucun professeur trouvé pour cette matière dans cette classe.'])->withInput();
        }

        // Préparer les données pour la création
        $gradeData = [
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'class_id' => $currentEnrollment->class_id,
            'teacher_id' => $teacher->id,
            'exam_type' => $validated['exam_type'],
            'term' => $validated['term'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'exam_date' => $validated['exam_date'],
            'comments' => $validated['comments'],
            'academic_year_id' => $academicYear->id
        ];

        $grade = StudentGrade::create($gradeData);

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
        try {
            // Si l'ID est numérique, c'est l'ancien format (note individuelle)
            if (is_numeric($id)) {
                $grade = StudentGrade::with(['student', 'subject', 'schoolClass', 'teacher'])->findOrFail($id);
                return view('grades.show', compact('grade'));
            }
            
            // Sinon, c'est un ID d'élève pour le bulletin
            $studentId = $id;
            $student = Student::with(['enrollments.schoolClass.level'])->findOrFail($studentId);
            $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
            
            if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
                return redirect()->route('grades.index')->with('error', 'L\'élève n\'est pas inscrit dans une classe active.');
            }
            
            // Récupérer toutes les notes de l'élève
            $grades = StudentGrade::with(['subject', 'teacher'])
                ->where('student_id', $studentId)
                ->where('class_id', $currentEnrollment->class_id)
                ->orderBy('subject_id')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Calculer la moyenne cumulée
            if ($grades->count() > 0) {
                $totalScore = $grades->sum('score');
                $totalMaxScore = $grades->sum('max_score');
                $cumulativeScore = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 20, 2) : 0;
                $cumulativePercentage = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 100, 1) : 0;
                
                // Déterminer la couleur de la note
                $cumulativeGradeColor = 'danger';
                if ($cumulativePercentage >= 80) {
                    $cumulativeGradeColor = 'success';
                } elseif ($cumulativePercentage >= 70) {
                    $cumulativeGradeColor = 'info';
                } elseif ($cumulativePercentage >= 60) {
                    $cumulativeGradeColor = 'primary';
                } elseif ($cumulativePercentage >= 50) {
                    $cumulativeGradeColor = 'warning';
                } elseif ($cumulativePercentage >= 40) {
                    $cumulativeGradeColor = 'secondary';
                }
            } else {
                $cumulativeScore = '--';
                $cumulativePercentage = 0;
                $cumulativeGradeColor = 'secondary';
            }
            
            $class = $currentEnrollment->schoolClass;
            $academicYear = AcademicYear::where('is_current', true)->first();
            
            // Préparer les données pour le PDF
            $tableDataForPDF = [];
            $totalsRowForPDF = [];
            
            if ($grades->count() > 0) {
                $gradesBySubject = $grades->groupBy('subject_id');
                
                foreach ($gradesBySubject as $subjectId => $subjectGrades) {
                    $subject = $subjectGrades->first()->subject;
                    $teacher = $subjectGrades->first()->teacher;
                    
                    // Calculer la moyenne de la matière
                    $subjectTotalScore = $subjectGrades->sum('score');
                    $subjectTotalMaxScore = $subjectGrades->sum('max_score');
                    $subjectAverage = $subjectTotalMaxScore > 0 ? round(($subjectTotalScore / $subjectTotalMaxScore) * 20, 2) : 0;
                    $subjectPercentage = $subjectTotalMaxScore > 0 ? round(($subjectTotalScore / $subjectTotalMaxScore) * 100, 1) : 0;
                    
                    // Déterminer l'appréciation
                    $appreciation = 'Insuffisant';
                    if ($subjectPercentage >= 80) $appreciation = 'Excellent';
                    elseif ($subjectPercentage >= 70) $appreciation = 'Très bien';
                    elseif ($subjectPercentage >= 60) $appreciation = 'Bien';
                    elseif ($subjectPercentage >= 50) $appreciation = 'Assez bien';
                    elseif ($subjectPercentage >= 40) $appreciation = 'Passable';
                    
                    // Coefficient (par défaut 1)
                    $coefficient = 1;
                    $noteCoeff = $subjectAverage * $coefficient;
                    
                    $tableDataForPDF[] = [
                        $subject->name,
                        $subjectAverage . '/20',
                        $coefficient,
                        number_format($noteCoeff, 2),
                        '--',
                        '0h00',
                        $appreciation,
                        $teacher->first_name . ' ' . $teacher->last_name
                    ];
                }
                
                // Préparer la ligne des totaux
                $totalCoeff = $gradesBySubject->count();
                $totalNoteCoeff = $grades->sum(function($grade) {
                    return ($grade->score / $grade->max_score) * 20;
                });
                
                $totalsRowForPDF = [
                    'TOTAUX',
                    $cumulativeScore . '/20',
                    $totalCoeff,
                    number_format($totalNoteCoeff, 2),
                    '--',
                    '0h00',
                    $cumulativePercentage >= 50 ? "Admis" : "Non admis",
                    ''
                ];
            }
            
            return view('grades.show', compact(
                'student', 
                'grades', 
                'class', 
                'academicYear', 
                'cumulativeScore', 
                'cumulativePercentage', 
                'cumulativeGradeColor',
                'tableDataForPDF',
                'totalsRowForPDF'
            ));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du bulletin', [
                'student_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('grades.index')->with('error', 'Erreur lors de l\'affichage du bulletin.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $grade = StudentGrade::findOrFail($id);
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
        $grade = StudentGrade::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'term' => 'required|in:1er trimestre,2ème trimestre,3ème trimestre',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
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
        $grade = StudentGrade::findOrFail($id);
        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Note supprimée avec succès !');
    }

    /**
     * Gérer les notes d'un élève (vue pour modifier/supprimer des notes individuelles)
     */
    public function manageStudentGrades(string $studentId)
    {
        try {
            $student = Student::with(['enrollments.schoolClass.level'])->findOrFail($studentId);
            $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
            
            if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
                return redirect()->route('grades.index')->with('error', 'L\'élève n\'est pas inscrit dans une classe active.');
            }
            
            // Récupérer toutes les notes de l'élève
            $grades = StudentGrade::with(['subject', 'teacher'])
                ->where('student_id', $studentId)
                ->where('class_id', $currentEnrollment->class_id)
                ->orderBy('subject_id')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $class = $currentEnrollment->schoolClass;
            $academicYear = AcademicYear::where('is_current', true)->first();
            
            return view('grades.manage-student', compact(
                'student', 
                'grades', 
                'class', 
                'academicYear'
            ));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la gestion des notes', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('grades.index')->with('error', 'Erreur lors de la gestion des notes.');
        }
    }

    /**
     * Supprimer toutes les notes d'un élève
     */
    public function deleteAllGradesForStudent(string $studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $deletedCount = StudentGrade::where('student_id', $studentId)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Toutes les notes de {$student->first_name} {$student->last_name} ont été supprimées avec succès.",
                    'deleted_count' => $deletedCount
                ]);
            }

            return redirect()->route('grades.index')
                ->with('success', "Toutes les notes de {$student->first_name} {$student->last_name} ont été supprimées avec succès.");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression des notes', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la suppression des notes'
                ], 500);
            }

            return redirect()->route('grades.index')
                ->with('error', 'Erreur lors de la suppression des notes.');
        }
    }

    /**
     * Afficher le bulletin de notes d'un élève
     */
    public function showBulletin(string $studentId)
    {
        try {
            Log::info('showBulletin called for student: ' . $studentId);
            $student = Student::with(['enrollments.schoolClass.level'])->findOrFail($studentId);
            Log::info('Student found: ' . $student->first_name . ' ' . $student->last_name);
            $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
            
            if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
                return redirect()->route('grades.index')->with('error', 'L\'élève n\'est pas inscrit dans une classe active.');
            }
            
            $class = $currentEnrollment->schoolClass;
            $academicYear = AcademicYear::where('is_current', true)->first();
            
            // ==================== STATISTIQUES DE LA CLASSE ====================
            // Récupérer tous les élèves de la classe
            $classStudents = Student::whereHas('enrollments', function ($query) use ($class) {
                $query->where('class_id', $class->id)->where('status', 'active');
            })->get();
            
            $totalStudents = $classStudents->count();
            $maleStudents = $classStudents->where('gender', 'male')->count();
            $femaleStudents = $classStudents->where('gender', 'female')->count();
            
            // ==================== INFORMATIONS COMPLÈTES DE L'ÉLÈVE ====================
            // Récupérer les informations complètes de l'élève
            $studentInfo = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'matricule' => $student->student_id ?? 'STU' . str_pad($student->id, 6, '0', STR_PAD_LEFT),
                'birth_date' => $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y') : 'N/C',
                'birth_place' => $student->place_of_birth ?? 'N/C',
                'gender' => $student->gender ? ucfirst($student->gender) : 'N/C',
                'nationality' => 'Gabonaise', // Par défaut
                'photo' => $student->photo,
                'avatar' => null
            ];
            
            // ==================== NOTES PAR TRIMESTRE ====================
            $trimesters = ['1er trimestre', '2ème trimestre', '3ème trimestre'];
            $trimesterData = [];
            
            foreach ($trimesters as $trimester) {
                $trimesterGrades = StudentGrade::with(['subject', 'teacher'])
                    ->where('student_id', $studentId)
                    ->where('class_id', $class->id)
                    ->where('term', $trimester)
                    ->orderBy('subject_id')
                    ->get();
                
                if ($trimesterGrades->count() > 0) {
                    $trimesterData[$trimester] = $this->calculateTrimesterGrades($trimesterGrades, $class, $studentId);
                }
            }
            
            // ==================== CALCUL DES RANGS ET MOYENNES DE CLASSE ====================
            $subjectRanks = $this->calculateSubjectRanks($class->id, $studentId);
            $classAverages = $this->calculateClassAverages($class->id);
            
            // ==================== COEFFICIENTS DES MATIÈRES ====================
            $subjectCoefficients = $this->getSubjectCoefficients($class->level_id);
            
            // ==================== PROFIL DE LA CLASSE ====================
            $classProfile = $this->calculateClassProfile($class->id);
            
            // ==================== BILAN GÉNÉRAL ====================
            $generalBalance = $this->calculateGeneralBalance($trimesterData);
            
            // ==================== PROFESSEUR PRINCIPAL DE LA CLASSE ====================
            $principalTeacher = $class->allTeachers()
                ->wherePivot('role', 'principal')
                ->first();
            
            $principalTeacherName = $principalTeacher ? 
                $principalTeacher->first_name . ' ' . $principalTeacher->last_name : 
                'N/C';
            
            // ==================== DONNÉES POUR LE PDF ====================
            $tableDataForPDF = [];
            $totalsRowForPDF = [];
            
            if (!empty($trimesterData)) {
                // Prendre le dernier trimestre avec des notes
                $lastTrimester = array_key_last($trimesterData);
                $currentTrimesterGrades = $trimesterData[$lastTrimester];
                
                foreach ($currentTrimesterGrades['subjects'] as $subjectData) {
                    $coefficient = $subjectCoefficients[$subjectData['subject_id']] ?? 1;
                    $noteCoeff = $subjectData['average'] * $coefficient;
                    $rank = $subjectRanks[$subjectData['subject_id']] ?? 'N/C';
                    
                    $tableDataForPDF[] = [
                        $subjectData['subject_name'],
                        $subjectData['average'] > 0 ? $subjectData['average'] . '/20' : 'N/C',
                        $coefficient,
                        $subjectData['average'] > 0 ? number_format($noteCoeff, 2) : 'N/C',
                        $rank,
                        '0h00', // Absences (à implémenter plus tard)
                        $this->getAppreciation($subjectData['average']),
                        $subjectData['teacher_name']
                    ];
                }
                
                // Ligne des totaux
                $totalCoeff = array_sum(array_column($currentTrimesterGrades['subjects'], 'coefficient'));
                $totalNoteCoeff = array_sum(array_column($currentTrimesterGrades['subjects'], 'note_coeff'));
                
                $totalsRowForPDF = [
                    'TOTAL',
                    $currentTrimesterGrades['cumulative_score'] . '/20',
                    $totalCoeff,
                    number_format($totalNoteCoeff, 2),
                    $currentTrimesterGrades['rank'] ?? 'N/C',
                    '0h00',
                    $this->getAppreciation($currentTrimesterGrades['cumulative_score']),
                    'PROFESSEUR PRINCIPAL'
                ];
            }
            
            Log::info('About to return view grades.show');
            return view('grades.show', compact(
                'student', 
                'class', 
                'academicYear',
                'totalStudents',
                'maleStudents',
                'femaleStudents',
                'studentInfo',
                'trimesterData',
                'subjectRanks',
                'classAverages',
                'subjectCoefficients',
                'classProfile',
                'generalBalance',
                'principalTeacherName',
                'tableDataForPDF',
                'totalsRowForPDF'
            ));
            
        } catch (\Exception $e) {
            Log::error('Exception in showBulletin: ' . $e->getMessage());
            Log::error('Erreur lors de l\'affichage du bulletin', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('grades.index')->with('error', 'Erreur lors de l\'affichage du bulletin.');
        }
    }

    /**
     * Obtenir l'appréciation basée sur la note
     */
    private function getAppreciation($score)
    {
        if ($score >= 16) return 'Excellent';
        if ($score >= 14) return 'Très bien';
        if ($score >= 12) return 'Bien';
        if ($score >= 10) return 'Assez bien';
        if ($score >= 8) return 'Passable';
        return 'Insuffisant';
    }

    /**
     * Obtenir les statistiques des notes
     */
    private function getGradeStats(Request $request)
    {
        $query = StudentGrade::query();

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
        $grades = StudentGrade::where('student_id', $studentId)
            ->with(['subject', 'schoolClass', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('grades.by-student', compact('student', 'grades'));
    }

    /**
     * Notes par classe
     */
    public function byClass(Request $request, $classId)
    {
        $class = SchoolClass::findOrFail($classId);
        $grades = StudentGrade::where('class_id', $classId)
            ->with(['student', 'subject', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('grades.by-class', compact('class', 'grades'));
    }

    // ==================== MÉTHODES API POUR LE FORMULAIRE DYNAMIQUE ====================

    /**
     * Récupérer tous les élèves avec leurs classes
     */
    public function getStudentsWithClasses()
    {
        try {
            $students = Student::with(['enrollments.schoolClass.level'])
                ->orderBy('first_name')
                ->get()
                ->map(function ($student) {
                    $currentEnrollment = $student->enrollments()
                        ->where('status', 'active')
                        ->with('schoolClass.level')
                        ->first();
                    
                    return [
                        'id' => $student->id,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'school_class_id' => $currentEnrollment ? $currentEnrollment->class_id : null,
                        'school_class' => $currentEnrollment && $currentEnrollment->schoolClass ? [
                            'id' => $currentEnrollment->schoolClass->id,
                            'name' => $currentEnrollment->schoolClass->name,
                            'level' => $currentEnrollment->schoolClass->level ? [
                                'id' => $currentEnrollment->schoolClass->level->id,
                                'name' => $currentEnrollment->schoolClass->level->name,
                                'cycle' => $currentEnrollment->schoolClass->level->cycle
                            ] : null
                        ] : null
                    ];
                });

            return response()->json($students);
        } catch (\Exception $e) {
            Log::error('Erreur dans getStudentsWithClasses: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des élèves'], 500);
        }
    }

    /**
     * Récupérer toutes les matières avec leurs professeurs
     */
    public function getSubjectsWithTeachers()
    {
        $subjects = Subject::with(['teachers.classes'])
            ->orderBy('name')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'teachers' => $subject->teachers->map(function ($teacher) {
                        return [
                            'id' => $teacher->id,
                            'first_name' => $teacher->first_name,
                            'last_name' => $teacher->last_name,
                            'specialization' => $teacher->specialization,
                            'classes' => $teacher->classes->map(function ($class) {
                                return [
                                    'id' => $class->id,
                                    'name' => $class->name
                                ];
                            })
                        ];
                    })
                ];
            });

        return response()->json($subjects);
    }

    /**
     * Récupérer les informations détaillées d'un élève
     */
    public function getStudentInfo($studentId)
    {
        try {
            $student = Student::with(['enrollments.schoolClass.levelData'])
                ->findOrFail($studentId);
            
            $currentEnrollment = $student->enrollments()
                ->where('status', 'active')
                ->with('schoolClass.levelData')
                ->first();

            return response()->json([
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'school_class' => $currentEnrollment && $currentEnrollment->schoolClass ? [
                    'id' => $currentEnrollment->schoolClass->id,
                    'name' => $currentEnrollment->schoolClass->name,
                    'level' => $currentEnrollment->schoolClass->levelData ? [
                        'id' => $currentEnrollment->schoolClass->levelData->id,
                        'name' => $currentEnrollment->schoolClass->levelData->name,
                        'cycle' => $currentEnrollment->schoolClass->levelData->cycle
                    ] : null
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations de l\'élève', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erreur lors de la récupération des informations de l\'élève',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les matières disponibles pour une classe
     */
    public function getClassSubjects($classId)
    {
        try {
            $class = SchoolClass::with(['levelData'])->findOrFail($classId);
            
            // Récupérer les enseignants assignés à cette classe
            $teachersInClass = \App\Models\Teacher::whereHas('classes', function ($query) use ($classId) {
                $query->where('classes.id', $classId);
            })->get();

            // Récupérer les spécialisations des enseignants
            $specializations = $teachersInClass->pluck('specialization')
                                              ->filter() // enlever les null
                                              ->unique()
                                              ->values();

            // Log pour debug
            Log::info('Enseignants et spécialisations trouvés', [
                'class_id' => $classId,
                'teachers' => $teachersInClass->pluck('full_name')->toArray(),
                'specializations' => $specializations->toArray()
            ]);

            // Récupérer les matières correspondant aux spécialisations
            $subjects = Subject::whereIn('name', $specializations)
                              ->orWhere(function($query) use ($specializations) {
                                  // Essayez aussi de matcher par le code ou une partie du nom
                                  foreach ($specializations as $spec) {
                                      $query->orWhere('name', 'LIKE', "%{$spec}%")
                                            ->orWhere('code', 'LIKE', "%{$spec}%");
                                  }
                              })
                              ->orderBy('name')
                              ->get();

            // Si aucune matière trouvée par spécialisation, récupérer les matières du cycle de la classe
            if ($subjects->isEmpty() && $class->levelData) {
                $subjects = Subject::where('cycle', $class->levelData->cycle)
                                  ->orderBy('name')
                                  ->get();
                
                Log::info('Aucune matière trouvée par spécialisation, utilisation du cycle', [
                    'cycle' => $class->levelData->cycle,
                    'subjects_count' => $subjects->count()
                ]);
            }

            // Log final
            Log::info('Matières finales pour la classe', [
                'class_id' => $classId,
                'subjects_count' => $subjects->count(),
                'subjects' => $subjects->pluck('name')->toArray()
            ]);

            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des matières', [
                'class_id' => $classId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Erreur lors de la récupération des matières'], 500);
        }
    }

    /**
     * Récupérer le professeur d'une matière pour une classe spécifique
     */
    public function getSubjectTeacherForClass($subjectId, $classId)
    {
        // D'abord, essayer de trouver un professeur qui enseigne cette matière dans cette classe
        $teacher = Teacher::whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subjects.id', $subjectId);
        })->whereHas('classes', function ($query) use ($classId) {
            $query->where('classes.id', $classId);
        })->first();

        // Si aucun professeur trouvé, prendre le premier professeur de cette matière
        if (!$teacher) {
            $teacher = Teacher::whereHas('subjects', function ($query) use ($subjectId) {
                $query->where('subjects.id', $subjectId);
            })->first();
        }

        if (!$teacher) {
            return response()->json(['error' => 'Aucun professeur trouvé pour cette matière'], 404);
        }

        return response()->json([
            'id' => $teacher->id,
            'first_name' => $teacher->first_name,
            'last_name' => $teacher->last_name,
            'specialization' => $teacher->specialization
        ]);
    }

    /**
     * Récupérer les classes d'un niveau
     */
    public function getClassesForLevel($levelId)
    {
        $classes = SchoolClass::where('level_id', $levelId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    /**
     * Récupérer les élèves d'une classe
     */
    public function getStudentsForClass($classId)
    {
        $students = Student::whereHas('enrollments', function ($query) use ($classId) {
            $query->where('class_id', $classId)
                  ->where('status', 'active');
        })->orderBy('first_name')
          ->get(['id', 'first_name', 'last_name']);

        return response()->json($students);
    }

    /**
     * Enregistrer plusieurs notes à la fois pour un élève
     */
    public function storeMultipleGrades(Request $request)
    {
        try {
                    // Log des données reçues pour debug
        Log::info('Données brutes reçues pour l\'enregistrement multiple', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_data' => $request->all(),
            'json_data' => $request->json() ? $request->json()->all() : null,
            'input_data' => $request->input(),
            'is_json' => $request->isJson()
        ]);

            // Validation des données principales
            try {
                $validated = $request->validate([
                    'student_id' => 'required|exists:students,id',
                    'term' => 'required|in:1er trimestre,2ème trimestre,3ème trimestre',
                    'grades' => 'required|array|min:1',
                    'grades.*.subject_id' => 'required|exists:subjects,id',
                    'grades.*.score' => 'required|numeric|min:0',
                    'grades.*.max_score' => 'required|numeric|min:1',
                    'grades.*.comments' => 'nullable|string|max:500'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Erreur de validation lors de l\'enregistrement multiple', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur de validation',
                    'validation_errors' => $e->errors()
                ], 422);
            }

            Log::info('Données reçues pour l\'enregistrement multiple', [
                'student_id' => $validated['student_id'],
                'term' => $validated['term'],
                'grades_count' => count($validated['grades'])
            ]);

            // Récupérer l'année académique active
            $academicYear = AcademicYear::where('is_current', true)->first();
            if (!$academicYear) {
                return response()->json(['error' => 'Aucune année académique active trouvée.'], 400);
            }

            // Récupérer l'élève et sa classe
            $student = Student::with(['enrollments.schoolClass'])->findOrFail($validated['student_id']);
            $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
            
            if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
                return response()->json(['error' => 'L\'élève n\'est pas inscrit dans une classe active.'], 400);
            }

            // Vérifier que l'enrollment a bien un class_id
            if (!$currentEnrollment->class_id) {
                Log::error('Enrollment sans class_id', [
                    'enrollment' => $currentEnrollment->toArray(),
                    'student_id' => $validated['student_id']
                ]);
                return response()->json(['error' => 'L\'inscription de l\'élève n\'a pas de classe associée.'], 400);
            }

            $createdGrades = [];
            $errors = [];

            // Traitement de chaque note
            foreach ($validated['grades'] as $index => $gradeData) {
                try {
                    // Vérifier que la note ne dépasse pas le maximum
                    if ($gradeData['score'] > $gradeData['max_score']) {
                        $errors[] = "Note #" . ($index + 1) . ": La note (" . $gradeData['score'] . ") ne peut pas dépasser le maximum (" . $gradeData['max_score'] . ").";
                        continue;
                    }

                    // Récupérer le professeur qui enseigne cette matière dans cette classe
                    $teacher = Teacher::whereHas('subjects', function ($query) use ($gradeData) {
                        $query->where('subjects.id', $gradeData['subject_id']);
                    })->whereHas('classes', function ($query) use ($currentEnrollment) {
                        $query->where('classes.id', $currentEnrollment->class_id);
                    })->first();

                    // Si pas de professeur trouvé, prendre le premier professeur de la matière
                    if (!$teacher) {
                        $teacher = Teacher::whereHas('subjects', function ($query) use ($gradeData) {
                            $query->where('subjects.id', $gradeData['subject_id']);
                        })->first();
                    }

                    if (!$teacher) {
                        $errors[] = "Note #" . ($index + 1) . ": Aucun professeur trouvé pour cette matière.";
                        continue;
                    }

                    // Préparer les données pour l'insertion
                    $gradeCreateData = [
                        'student_id' => $validated['student_id'],
                        'subject_id' => $gradeData['subject_id'],
                        'class_id' => $currentEnrollment->class_id,
                        'teacher_id' => $teacher->id,
                        'term' => $validated['term'],
                        'score' => $gradeData['score'],
                        'max_score' => $gradeData['max_score'],
                        'comments' => $gradeData['comments'] ?? '',
                        'academic_year_id' => $academicYear->id
                    ];

                    Log::info('Données avant insertion Grade', [
                        'grade_data' => $gradeCreateData,
                        'academic_year' => $academicYear ? $academicYear->toArray() : 'null'
                    ]);

                    // Créer la note
                    $grade = StudentGrade::create($gradeCreateData);

                    $createdGrades[] = $grade;

                } catch (\Exception $e) {
                    Log::error('Erreur lors de la création de la note', [
                        'index' => $index,
                        'error' => $e->getMessage(),
                        'grade_data' => $gradeData
                    ]);
                    $errors[] = "Note #" . ($index + 1) . ": " . $e->getMessage();
                }
            }

            // Réponse finale
            if (count($createdGrades) > 0) {
                $message = count($createdGrades) . ' note(s) enregistrée(s) avec succès';
                if (count($errors) > 0) {
                    $message .= ', ' . count($errors) . ' erreur(s) détectée(s)';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'created_count' => count($createdGrades),
                    'error_count' => count($errors),
                    'errors' => $errors
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucune note n\'a pu être enregistrée',
                    'errors' => $errors
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erreur générale lors de l\'enregistrement multiple', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'enregistrement des notes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déterminer le trimestre actuel basé sur la date
     */
    private function getCurrentTerm()
    {
        $currentMonth = date('n'); // 1-12
        
        if ($currentMonth >= 9 || $currentMonth <= 12) {
            return 1; // Premier trimestre (Septembre-Décembre)
        } elseif ($currentMonth >= 1 && $currentMonth <= 3) {
            return 2; // Deuxième trimestre (Janvier-Mars)
        } else {
            return 3; // Troisième trimestre (Avril-Juin)
        }
    }

    /**
     * Obtenir le label du trimestre
     */
    private function getTermLabel($term)
    {
        switch ($term) {
            case 1:
                return '1er TRIMESTRE';
            case 2:
                return '2ème TRIMESTRE';
            case 3:
                return '3ème TRIMESTRE';
            default:
                return '3ème TRIMESTRE';
        }
    }

    /**
     * Calculer les notes d'un trimestre
     */
    private function calculateTrimesterGrades($grades, $class, $studentId)
    {
        $subjects = [];
        $totalScore = 0;
        $totalMaxScore = 0;
        
        // Grouper par matière
        $gradesBySubject = $grades->groupBy('subject_id');
        
        foreach ($gradesBySubject as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            $teacher = $subjectGrades->first()->teacher;
            
            // Calculer la moyenne de la matière
            $subjectTotalScore = $subjectGrades->sum('score');
            $subjectTotalMaxScore = $subjectGrades->sum('max_score');
            $subjectAverage = $subjectTotalMaxScore > 0 ? round(($subjectTotalScore / $subjectTotalMaxScore) * 20, 2) : 0;
            
            $subjects[] = [
                'subject_id' => $subjectId,
                'subject_name' => $subject->name,
                'average' => $subjectAverage,
                'teacher_name' => $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'N/A',
                'coefficient' => 1, // Sera mis à jour avec les vrais coefficients
                'note_coeff' => $subjectAverage,
                'rank' => null // Sera calculé séparément
            ];
            
            $totalScore += $subjectTotalScore;
            $totalMaxScore += $subjectTotalMaxScore;
        }
        
        $cumulativeScore = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 20, 2) : 0;
        
        return [
            'subjects' => $subjects,
            'cumulative_score' => $cumulativeScore,
            'total_subjects' => count($subjects)
        ];
    }
    
    /**
     * Calculer les rangs par matière
     */
    private function calculateSubjectRanks($classId, $studentId)
    {
        $ranks = [];
        
        // Récupérer toutes les matières qui ont des notes dans cette classe
        $subjectsWithGrades = StudentGrade::where('class_id', $classId)
            ->select('subject_id')
            ->distinct()
            ->get()
            ->pluck('subject_id');
        
        $subjects = Subject::whereIn('id', $subjectsWithGrades)->get();
        
        foreach ($subjects as $subject) {
            // Récupérer tous les élèves avec des notes dans cette matière
            $studentAverages = StudentGrade::where('class_id', $classId)
                ->where('subject_id', $subject->id)
                ->selectRaw('student_id, AVG((score / max_score) * 20) as average')
                ->groupBy('student_id')
                ->orderByDesc('average')
                ->get();
            
            if ($studentAverages->count() > 0) {
                // Trouver le rang de l'élève
                $rank = 1;
                foreach ($studentAverages as $avg) {
                    if ($avg->student_id == $studentId) {
                        $ranks[$subject->id] = $rank;
                        break;
                    }
                    $rank++;
                }
            }
        }
        
        return $ranks;
    }
    
    /**
     * Calculer les moyennes de classe par matière
     */
    private function calculateClassAverages($classId)
    {
        $averages = [];
        
        $subjectAverages = StudentGrade::where('class_id', $classId)
            ->selectRaw('subject_id, AVG((score / max_score) * 20) as class_average')
            ->groupBy('subject_id')
            ->get();
        
        foreach ($subjectAverages as $avg) {
            $averages[$avg->subject_id] = round($avg->class_average, 2);
        }
        
        return $averages;
    }
    
    /**
     * Obtenir les coefficients des matières
     */
    private function getSubjectCoefficients($levelId)
    {
        // Coefficients par défaut selon le niveau
        $coefficients = [
            // Matières principales
            'mathématiques' => 4,
            'français' => 4,
            'histoire' => 2,
            'géographie' => 2,
            'sciences' => 3,
            'anglais' => 2,
            'espagnol' => 2,
            'allemand' => 2,
            'arts plastiques' => 1,
            'musique' => 1,
            'éducation physique et sportive' => 1,
            'technologie' => 1,
            'sciences de la vie et de la terre' => 2,
            'physique-chimie' => 2,
            'physique' => 2,
            'chimie' => 2,
            'svt' => 2,
            'eps' => 1,
            'arts' => 1,
            'informatique' => 1,
            'philosophie' => 3,
            'littérature' => 3,
            'langues vivantes' => 2,
            'histoire-géographie' => 2,
        ];
        
        return $coefficients;
    }
    
    /**
     * Calculer le profil de la classe
     */
    private function calculateClassProfile($classId)
    {
        // Récupérer toutes les notes de la classe
        $allGrades = StudentGrade::where('class_id', $classId)->get();
        
        if ($allGrades->count() == 0) {
            return [
                'moyenne_classe' => 0,
                'meilleure_note' => 0,
                'plus_basse_note' => 0,
                'ecart_type' => 0
            ];
        }
        
        // Calculer les statistiques
        $grades = $allGrades->map(function ($grade) {
            return ($grade->score / $grade->max_score) * 20;
        });
        
        $moyenne = $grades->avg();
        $max = $grades->max();
        $min = $grades->min();
        
        // Calculer l'écart-type
        $variance = $grades->map(function ($grade) use ($moyenne) {
            return pow($grade - $moyenne, 2);
        })->avg();
        $ecartType = sqrt($variance);
        
        return [
            'moyenne_classe' => round($moyenne, 2),
            'meilleure_note' => round($max, 2),
            'plus_basse_note' => round($min, 2),
            'ecart_type' => round($ecartType, 2)
        ];
    }
    
    /**
     * Calculer le bilan général
     */
    private function calculateGeneralBalance($trimesterData)
    {
        if (empty($trimesterData)) {
            return [
                'trimestres_disponibles' => [],
                'moyenne_generale' => 0,
                'evolution' => 'N/C'
            ];
        }
        
        $trimestres = array_keys($trimesterData);
        $moyennes = array_column($trimesterData, 'cumulative_score');
        
        $moyenneGenerale = array_sum($moyennes) / count($moyennes);
        
        // Calculer l'évolution
        $evolution = 'N/C';
        if (count($moyennes) > 1) {
            $diff = end($moyennes) - reset($moyennes);
            if ($diff > 0) {
                $evolution = 'En progression';
            } elseif ($diff < 0) {
                $evolution = 'En régression';
            } else {
                $evolution = 'Stable';
            }
        }
        
        return [
            'trimestres_disponibles' => $trimestres,
            'moyenne_generale' => round($moyenneGenerale, 2),
            'evolution' => $evolution
        ];
    }
    
    /**
     * Générer un matricule unique pour le bulletin
     */
    private function generateBulletinMatricule($studentId, $academicYearId)
    {
        // Générer un matricule de 12 chiffres : YYYYSSSSTTTT
        // YYYY = année, SSSS = student ID paddé, TTTT = timestamp modifié
        $year = date('Y');
        $studentPart = str_pad($studentId, 4, '0', STR_PAD_LEFT);
        $timePart = str_pad(substr(time(), -4), 4, '0', STR_PAD_LEFT);
        
        return $year . $studentPart . $timePart;
    }
}
