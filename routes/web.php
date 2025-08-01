<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EnrollmentController;

// Routes d'authentification (publiques)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Route de déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirection de la page d'accueil vers le dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour la gestion des élèves
    Route::resource('students', StudentController::class)->names([
        'index' => 'students.index',
        'create' => 'students.create',
        'store' => 'students.store',
        'show' => 'students.show',
        'edit' => 'students.edit',
        'update' => 'students.update',
        'destroy' => 'students.destroy',
    ]);

    // Routes pour la gestion des enseignants
    Route::resource('teachers', TeacherController::class)->names([
        'index' => 'teachers.index',
        'create' => 'teachers.create',
        'store' => 'teachers.store',
        'show' => 'teachers.show',
        'edit' => 'teachers.edit',
        'update' => 'teachers.update',
        'destroy' => 'teachers.destroy',
    ]);

    // Routes pour la gestion des parents
    Route::resource('parents', ParentController::class)->names([
        'index' => 'parents.index',
        'create' => 'parents.create',
        'store' => 'parents.store',
        'show' => 'parents.show',
        'edit' => 'parents.edit',
        'update' => 'parents.update',
        'destroy' => 'parents.destroy',
    ]);

    // Routes pour la gestion des classes
    Route::resource('classes', ClassController::class)->names([
        'index' => 'classes.index',
        'create' => 'classes.create',
        'store' => 'classes.store',
        'show' => 'classes.show',
        'edit' => 'classes.edit',
        'update' => 'classes.update',
        'destroy' => 'classes.destroy',
    ]);

    // Routes pour la gestion des notes
    Route::resource('grades', GradeController::class)->names([
        'index' => 'grades.index',
        'create' => 'grades.create',
        'store' => 'grades.store',
        'show' => 'grades.show',
        'edit' => 'grades.edit',
        'update' => 'grades.update',
        'destroy' => 'grades.destroy',
    ]);

    // Routes pour la gestion des présences
    Route::resource('attendances', AttendanceController::class)->names([
        'index' => 'attendances.index',
        'create' => 'attendances.create',
        'store' => 'attendances.store',
        'show' => 'attendances.show',
        'edit' => 'attendances.edit',
        'update' => 'attendances.update',
        'destroy' => 'attendances.destroy',
    ]);

    // Routes pour la gestion des frais
    Route::resource('fees', FeeController::class)->names([
        'index' => 'fees.index',
        'create' => 'fees.create',
        'store' => 'fees.store',
        'show' => 'fees.show',
        'edit' => 'fees.edit',
        'update' => 'fees.update',
        'destroy' => 'fees.destroy',
    ]);

    // Routes pour la gestion des paiements
    Route::resource('payments', PaymentController::class)->names([
        'index' => 'payments.index',
        'create' => 'payments.create',
        'store' => 'payments.store',
        'show' => 'payments.show',
        'edit' => 'payments.edit',
        'update' => 'payments.update',
        'destroy' => 'payments.destroy',
    ]);

    // Routes pour la gestion des matières
    Route::resource('subjects', SubjectController::class)->names([
        'index' => 'subjects.index',
        'create' => 'subjects.create',
        'store' => 'subjects.store',
        'show' => 'subjects.show',
        'edit' => 'subjects.edit',
        'update' => 'subjects.update',
        'destroy' => 'subjects.destroy',
    ]);

    // Route pour obtenir les matières par niveau
    Route::get('/levels/{level}/subjects', [SubjectController::class, 'byLevel'])->name('subjects.byLevel');

    // Routes pour la gestion des inscriptions
    Route::resource('enrollments', EnrollmentController::class)->names([
        'index' => 'enrollments.index',
        'create' => 'enrollments.create',
        'store' => 'enrollments.store',
        'show' => 'enrollments.show',
        'edit' => 'enrollments.edit',
        'update' => 'enrollments.update',
        'destroy' => 'enrollments.destroy',
    ]);
    
    // Routes spéciales pour les réinscriptions
    Route::get('/students/{student}/re-enroll', [EnrollmentController::class, 'reEnroll'])->name('enrollments.re-enroll');
    Route::post('/students/{student}/re-enroll', [EnrollmentController::class, 'processReEnrollment'])->name('enrollments.process-re-enrollment');
    Route::get('/enrollments/unenrolled-students', [EnrollmentController::class, 'getUnEnrolledStudents'])->name('enrollments.unenrolled');
    
    // Routes pour le nouveau workflow inscription-première
    Route::get('/enrollments/pending-students', [EnrollmentController::class, 'pendingStudentCreations'])->name('enrollments.pending-students');
    Route::get('/enrollments/{enrollment}/create-student', [EnrollmentController::class, 'showStudentCreationForm'])->name('enrollments.create-student');
    Route::post('/enrollments/{enrollment}/create-student', [EnrollmentController::class, 'createStudentFromEnrollment'])->name('enrollments.store-student');
    Route::post('/enrollments/{enrollment}/mark-pending', [EnrollmentController::class, 'markAsPending'])->name('enrollments.mark-pending');
    
    // Routes pour la génération de reçus
    Route::get('/enrollments/{enrollment}/receipt', [EnrollmentController::class, 'generateReceipt'])->name('enrollments.receipt');
    Route::get('/enrollments/{enrollment}/receipt/download', [EnrollmentController::class, 'downloadReceipt'])->name('enrollments.download-receipt');

    // Routes API pour les données dynamiques (optionnel)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
        Route::get('/students/classes-by-level', [StudentController::class, 'getClassesByLevel'])->name('students.classesByLevel');
        Route::get('/students/classes-by-cycle', [StudentController::class, 'getClassesByCycle'])->name('students.classesByCycle');
        Route::get('/teachers/search', [TeacherController::class, 'search'])->name('teachers.search');
        Route::get('/teachers/classes-by-cycle', [TeacherController::class, 'getClassesByCycle'])->name('teachers.classesByCycle');
        Route::get('/levels-by-cycle', function(\Illuminate\Http\Request $request) {
            return \App\Models\Level::where('cycle', $request->cycle)->get();
        })->name('levelsByCycle');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    });
});
