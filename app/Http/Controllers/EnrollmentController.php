<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Afficher la liste des inscriptions
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'schoolClass.level', 'academicYear']);
        
        // Filtres
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year_id', $request->academic_year);
        }
        
        if ($request->has('cycle') && $request->cycle) {
            $query->whereHas('schoolClass.level', function($q) use ($request) {
                $q->where('cycle', $request->cycle);
            });
        }
        
        if ($request->has('class') && $request->class) {
            $query->where('class_id', $request->class);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $enrollments = $query->orderBy('enrollment_date', 'desc')->paginate(15);
        
        // Données pour les filtres
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->active()->get();
        
        // Statistiques
        $totalEnrollments = Enrollment::count();
        $activeEnrollments = Enrollment::where('status', 'active')->count();
        $currentYearEnrollments = Enrollment::whereHas('academicYear', function($q) {
            $q->where('is_current', true);
        })->count();
        
        // Statistiques par cycle pour l'année courante
        $enrollmentsByCycle = [
            'preprimaire' => Enrollment::whereHas('schoolClass.level', function($q) {
                $q->where('cycle', 'preprimaire');
            })->whereHas('academicYear', function($q) {
                $q->where('is_current', true);
            })->count(),
            'primaire' => Enrollment::whereHas('schoolClass.level', function($q) {
                $q->where('cycle', 'primaire');
            })->whereHas('academicYear', function($q) {
                $q->where('is_current', true);
            })->count(),
            'college' => Enrollment::whereHas('schoolClass.level', function($q) {
                $q->where('cycle', 'college');
            })->whereHas('academicYear', function($q) {
                $q->where('is_current', true);
            })->count(),
            'lycee' => Enrollment::whereHas('schoolClass.level', function($q) {
                $q->where('cycle', 'lycee');
            })->whereHas('academicYear', function($q) {
                $q->where('is_current', true);
            })->count(),
        ];
        
        // Compter les inscriptions en attente de création d'élève
        $pendingCount = Enrollment::pendingStudentCreation()->count();
        
        return view('enrollments.index', compact(
            'enrollments',
            'academicYears',
            'levels',
            'classes',
            'totalEnrollments',
            'activeEnrollments',
            'currentYearEnrollments',
            'enrollmentsByCycle',
            'pendingCount'
        ));
    }

    /**
     * Afficher le formulaire d'inscription (nouveau workflow: inscription d'abord)
     */
    public function create()
    {
        $academicYears = AcademicYear::where('status', 'active')->get();
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->where('is_active', true)->get();
        
        return view('enrollments.create', compact('academicYears', 'levels', 'classes'));
    }

    /**
     * Enregistrer une nouvelle inscription (workflow: inscription d'abord)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Informations de l'inscrit
            'applicant_first_name' => 'required|string|max:255',
            'applicant_last_name' => 'required|string|max:255',
            'applicant_date_of_birth' => 'required|date',
            'applicant_gender' => 'required|in:male,female',
            'applicant_phone' => 'nullable|string|max:255',
            'applicant_email' => 'nullable|email|max:255',
            'applicant_address' => 'required|string',
            
            // Informations du parent/tuteur
            'parent_first_name' => 'required|string|max:255',
            'parent_last_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:255',
            'parent_email' => 'nullable|email|max:255',
            'parent_relationship' => 'required|in:father,mother,guardian,other',
            
            // Informations de l'inscription
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string',
            
            // Informations de paiement
            'total_fees' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,other',
            'payment_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string',
            'payment_due_date' => 'nullable|date|after_or_equal:enrollment_date'
        ]);

        // Vérifier s'il n'y a pas déjà une inscription pour cette personne
        $existingEnrollment = Enrollment::where('applicant_first_name', $validated['applicant_first_name'])
                                      ->where('applicant_last_name', $validated['applicant_last_name'])
                                      ->where('applicant_date_of_birth', $validated['applicant_date_of_birth'])
                                      ->where('academic_year_id', $validated['academic_year_id'])
                                      ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Une inscription existe déjà pour cette personne pour cette année scolaire.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Créer l'inscription avec statut "pending"
            $enrollmentData = $validated;
            $enrollmentData['enrollment_status'] = 'pending';
            $enrollmentData['status'] = 'active';
            $enrollmentData['is_new_enrollment'] = true;
            $enrollmentData['student_id'] = null; // Pas encore d'élève créé
            
            // Calculer le reste à percevoir
            $enrollmentData['balance_due'] = $validated['total_fees'] - $validated['amount_paid'];
            
            $enrollment = Enrollment::create($enrollmentData);
            
            // Mettre à jour le statut de paiement et générer les références automatiquement
            $enrollment->updatePaymentStatus();
            $enrollment->generateReceiptNumber();
            $enrollment->generatePaymentReference(); // Génération automatique de la référence
            $enrollment->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Inscription enregistrée avec succès! Voulez-vous créer le profil élève maintenant ?',
                'enrollment' => $enrollment->load(['schoolClass.level', 'academicYear']),
                'show_student_creation' => true,
                'receipt_number' => $enrollment->receipt_number
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails d'une inscription
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'schoolClass.level', 'academicYear']);
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Afficher le formulaire de modification d'inscription
     */
    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'schoolClass.level', 'academicYear']);
        $academicYears = AcademicYear::where('status', 'active')->get();
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->active()->get();
        
        return view('enrollments.edit', compact('enrollment', 'academicYears', 'levels', 'classes'));
    }

    /**
     * Mettre à jour une inscription
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,transferred,graduated',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $enrollment->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Inscription mise à jour avec succès!',
                'enrollment' => $enrollment->load(['student', 'schoolClass.level', 'academicYear'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une inscription
     */
    public function destroy(Enrollment $enrollment)
    {
        try {
            $enrollment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Inscription supprimée avec succès!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le formulaire de réinscription
     */
    public function reEnroll(Student $student)
    {
        // Récupérer la dernière inscription de l'étudiant
        $lastEnrollment = $student->enrollments()
                                ->with(['schoolClass.level', 'academicYear'])
                                ->orderBy('enrollment_date', 'desc')
                                ->first();
        
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->active()->get();
        
        return view('enrollments.re-enroll', compact('student', 'lastEnrollment', 'currentAcademicYear', 'levels', 'classes'));
    }

    /**
     * Traiter la réinscription
     */
    public function processReEnrollment(Request $request, Student $student)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Vérifier si l'étudiant n'est pas déjà inscrit pour cette année
        $existingEnrollment = $student->enrollments()
                                    ->where('academic_year_id', $validated['academic_year_id'])
                                    ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'L\'élève est déjà inscrit pour cette année scolaire.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Créer la nouvelle inscription
            $enrollment = $student->enrollments()->create([
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'enrollment_date' => $validated['enrollment_date'],
                'status' => 'active',
                'notes' => $validated['notes'] ?? 'Réinscription'
            ]);
            
            // Mettre à jour le statut de l'étudiant
            $student->update(['status' => 'active']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Réinscription effectuée avec succès!',
                'enrollment' => $enrollment->load(['student', 'schoolClass.level', 'academicYear'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les étudiants non inscrits pour l'année courante
     */
    public function getUnEnrolledStudents()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune année scolaire courante définie.'
            ]);
        }

        $unEnrolledStudents = Student::where('status', 'active')
                                   ->whereDoesntHave('enrollments', function($q) use ($currentYear) {
                                       $q->where('academic_year_id', $currentYear->id);
                                   })
                                   ->with(['enrollments.schoolClass.level', 'enrollments.academicYear'])
                                   ->orderBy('last_name')
                                   ->get();

        return response()->json([
            'success' => true,
            'students' => $unEnrolledStudents,
            'current_year' => $currentYear
        ]);
    }

    /**
     * Rechercher des inscriptions
     */
    public function search(Request $request)
    {
        $query = Enrollment::with(['student', 'schoolClass.level', 'academicYear']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year_id', $request->academic_year);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->get();

        return response()->json($enrollments);
    }

    /**
     * Afficher le formulaire de création d'élève depuis une inscription
     */
    public function showStudentCreationForm(Enrollment $enrollment)
    {
        if (!$enrollment->canCreateStudent()) {
            return redirect()->route('enrollments.index')
                           ->with('error', 'Cette inscription ne permet pas la création d\'un élève.');
        }

        return view('enrollments.create-student', compact('enrollment'));
    }

    /**
     * Créer un élève depuis une inscription
     */
    public function createStudentFromEnrollment(Request $request, Enrollment $enrollment)
    {
        if (!$enrollment->canCreateStudent()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette inscription ne permet pas la création d\'un élève.'
            ], 422);
        }

        $validated = $request->validate([
            'student_id' => 'nullable|unique:students', // Optionnel, sera généré automatiquement
            'place_of_birth' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Préparer les données de l'élève
            $studentData = $enrollment->getStudentDataForCreation();
            
            // Générer automatiquement le matricule si non fourni
            $studentData['student_id'] = $validated['student_id'] ?? Student::generateStudentId();
            
            $studentData['place_of_birth'] = $validated['place_of_birth'];
            $studentData['emergency_contact'] = $validated['emergency_contact'];
            $studentData['medical_conditions'] = $validated['medical_conditions'];

            // Gérer l'upload de photo
            if ($request->hasFile('photo')) {
                $studentData['photo'] = $request->file('photo')->store('students/photos', 'public');
            }

            // Créer l'élève
            $student = Student::create($studentData);

            // Créer ou associer le parent
            $parentData = $enrollment->getParentDataForCreation();
            
            // Vérifier si un parent similaire existe déjà
            $existingParent = ParentModel::where('first_name', $parentData['first_name'])
                                        ->where('last_name', $parentData['last_name'])
                                        ->where('phone', $parentData['phone'])
                                        ->first();

            if ($existingParent) {
                $parent = $existingParent;
            } else {
                $parent = ParentModel::create($parentData);
            }

            // Associer l'élève au parent
            $student->parents()->attach($parent->id);

            // Mettre à jour l'inscription
            $enrollment->markAsStudentCreated($student->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Élève créé avec succès depuis l\'inscription!',
                'student' => $student,
                'parent' => $parent,
                'enrollment' => $enrollment->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'élève: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les inscriptions en attente de création d'élève
     */
    public function pendingStudentCreations()
    {
        $pendingEnrollments = Enrollment::pendingStudentCreation()
                                      ->with(['schoolClass.level', 'academicYear'])
                                      ->orderBy('enrollment_date', 'desc')
                                      ->paginate(15);

        return view('enrollments.pending-students', compact('pendingEnrollments'));
    }

    /**
     * Marquer une inscription comme "en attente" (pas de création d'élève pour l'instant)
     */
    public function markAsPending(Enrollment $enrollment)
    {
        if ($enrollment->canCreateStudent()) {
            $enrollment->update(['enrollment_status' => 'pending']);
            
            return response()->json([
                'success' => true,
                'message' => 'Inscription marquée comme en attente.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cette inscription ne peut pas être marquée comme en attente.'
        ], 422);
    }

    /**
     * Générer et télécharger le reçu d'inscription
     */
    public function generateReceipt(Enrollment $enrollment)
    {
        // S'assurer qu'un numéro de reçu existe
        if (!$enrollment->receipt_number) {
            $enrollment->generateReceiptNumber();
        }

        return view('enrollments.receipt', compact('enrollment'));
    }

    /**
     * Télécharger le reçu en PDF
     */
    public function downloadReceipt(Enrollment $enrollment)
    {
        // S'assurer qu'un numéro de reçu existe
        if (!$enrollment->receipt_number) {
            $enrollment->generateReceiptNumber();
        }

        // Charger les relations nécessaires
        $enrollment->load(['schoolClass.level', 'academicYear']);

        // Générer le PDF avec DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('enrollments.receipt-pdf', compact('enrollment'));
        
        // Configuration pour format A5
        $pdf->setPaper('A5', 'portrait');
        
        // Nom du fichier
        $filename = 'recu_inscription_' . $enrollment->receipt_number . '.pdf';
        
        // Télécharger le PDF
        return $pdf->download($filename);
    }

    /**
     * Exporter les inscriptions
     */
    public function export(Request $request)
    {
        // Cette méthode pourra être étendue pour exporter en CSV/Excel
        $enrollments = Enrollment::with(['student', 'schoolClass.level', 'academicYear'])
                                ->when($request->academic_year, function($q, $year) {
                                    return $q->where('academic_year_id', $year);
                                })
                                ->get();

        return response()->json([
            'success' => true,
            'data' => $enrollments,
            'message' => 'Données exportées avec succès!'
        ]);
    }
} 