<?php

namespace App\Http\Controllers;

use App\Models\ParentAccount;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\OnlinePayment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParentPortalController extends Controller
{
    /**
     * Afficher la page de connexion du portail parent
     */
    public function showLogin()
    {
        return view('parent-portal.login');
    }

    /**
     * Traiter la connexion du parent
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Vérifier que l'utilisateur existe et a le rôle 'parent'
        $user = User::where('email', $request->email)
            ->where('role', 'parent')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucun compte parent trouvé avec cet email.'])->withInput();
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Mot de passe incorrect.'])->withInput();
        }

        // Trouver le parent associé à cet utilisateur
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return back()->withErrors(['email' => 'Aucun profil parent trouvé pour cet utilisateur.'])->withInput();
        }

        // Authentifier l'utilisateur avec Laravel
        Auth::login($user);

        // Créer une session pour le parent
        session(['parent_id' => $parent->id]);
        session(['user_id' => $user->id]);

        // Mettre à jour la dernière connexion
        $user->update(['last_login_at' => now()]);

        return redirect()->route('parent-portal.dashboard');
    }

    /**
     * Déconnexion du parent
     */
    public function logout()
    {
        Auth::logout();
        session()->forget(['parent_id', 'user_id']);
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('parent-portal.login');
    }

    /**
     * Dashboard principal du parent
     */
    public function dashboard()
    {
        $user = Auth::user();
        $parent = $this->getCurrentParent();
        $children = $parent->students;

        // Statistiques des enfants
        $stats = [
            'total_children' => $children->count(),
            'active_enrollments' => $children->where('status', 'active')->count(),
            'total_payments' => OnlinePayment::where('parent_id', $parent->id)->count(),
            'completed_payments' => OnlinePayment::where('parent_id', $parent->id)
                ->where('status', 'completed')->count()
        ];

        // Paiements récents
        $recentPayments = OnlinePayment::where('parent_id', $parent->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('parent-portal.dashboard', compact('user', 'parent', 'children', 'stats', 'recentPayments'));
    }

    /**
     * Afficher les informations d'un enfant
     */
    public function childDetails($studentId)
    {
        $parent = $this->getCurrentParent();
        
        $student = $parent->students()->where('students.id', $studentId)->first();
        
        if (!$student) {
            abort(404, 'Élève non trouvé.');
        }

        // Informations actuelles
        $currentEnrollment = $student->enrollments()
            ->with(['schoolClass.level', 'academicYear'])
            ->where('status', 'active')
            ->first();

        // Notes récentes
        $recentGrades = $student->grades()
            ->with(['subject', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Présences du mois
        $monthlyAttendance = $student->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();

        // Paiements de l'élève
        $studentPayments = OnlinePayment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent-portal.child-details', compact(
            'parent', 
            'student', 
            'currentEnrollment', 
            'recentGrades', 
            'monthlyAttendance',
            'studentPayments'
        ));
    }

    /**
     * Afficher les notes d'un enfant
     */
    public function childGrades($studentId)
    {
        $parent = $this->getCurrentParent();
        
        $student = $parent->students()->where('students.id', $studentId)->first();
        
        if (!$student) {
            abort(404, 'Élève non trouvé.');
        }

        $grades = $student->grades()
            ->with(['subject', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('parent-portal.child-grades', compact('parent', 'student', 'grades'));
    }

    /**
     * Afficher les présences d'un enfant
     */
    public function childAttendance($studentId)
    {
        $parent = $this->getCurrentParent();
        
        $student = $parent->students()->where('students.id', $studentId)->first();
        
        if (!$student) {
            abort(404, 'Élève non trouvé.');
        }

        $month = request('month', now()->format('Y-m'));
        $date = \Carbon\Carbon::createFromFormat('Y-m', $month);

        $attendance = $student->attendances()
            ->whereYear('attendance_date', $date->year)
            ->whereMonth('attendance_date', $date->month)
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('parent-portal.child-attendance', compact('parent', 'student', 'attendance', 'date'));
    }

    /**
     * Afficher l'historique des paiements
     */
    public function paymentHistory()
    {
        $parent = $this->getCurrentParent();

        $payments = OnlinePayment::where('parent_id', $parent->id)
            ->with(['student', 'enrollment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('parent-portal.payment-history', compact('parent', 'payments'));
    }

    /**
     * Afficher le profil du parent
     */
    public function profile()
    {
        $parent = $this->getCurrentParent();

        $user = Auth::user();
        return view('parent-portal.profile', compact('user', 'parent'));
    }

    /**
     * Mettre à jour le profil du parent
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $parent = $this->getCurrentParent();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mettre à jour l'utilisateur
        $user->update([
            'email' => $request->email,
            'name' => $request->first_name . ' ' . $request->last_name
        ]);

        // Mettre à jour le parent
        $parent->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Afficher la page d'inscription en ligne
     */
    public function showOnlineEnrollment()
    {
        $levels = \App\Models\Level::active()->orderBy('order')->get();
        $classes = \App\Models\SchoolClass::with('level')->active()->get();
        $academicYears = \App\Models\AcademicYear::where('status', 'active')->get();
        $paymentGateways = \App\Models\PaymentGateway::getActiveGateways();

        return view('parent-portal.online-enrollment', compact('levels', 'classes', 'academicYears', 'paymentGateways'));
    }

    /**
     * Traiter l'inscription en ligne
     */
    public function processOnlineEnrollment(Request $request)
    {
        $rules = [
            'enrollment_type' => 'required|in:new,renewal',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'payment_method' => 'required|in:moov_money,airtel_money',
            'payer_phone' => 'required|string|max:20'
        ];

        // Règles spécifiques selon le type d'inscription
        if ($request->enrollment_type === 'new') {
            $rules = array_merge($rules, [
                'student_first_name' => 'required|string|max:255',
                'student_last_name' => 'required|string|max:255',
                'student_date_of_birth' => 'required|date',
                'student_gender' => 'required|in:male,female',
                'parent_first_name' => 'required|string|max:255',
                'parent_last_name' => 'required|string|max:255',
                'parent_phone' => 'required|string|max:20',
                'parent_email' => 'required|email'
            ]);
        } else {
            $rules = array_merge($rules, [
                'existing_student_id' => 'required|exists:students,id',
                'parent_first_name' => 'required|string|max:255',
                'parent_last_name' => 'required|string|max:255',
                'parent_phone' => 'required|string|max:20',
                'parent_email' => 'required|email'
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isRenewal = $request->enrollment_type === 'renewal';
        $amount = $isRenewal ? 30000 : 50000; // Réinscription moins chère
        
        // Créer l'inscription en attente
        $enrollmentData = [
            'class_id' => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'enrollment_status' => 'pending_payment',
            'parent_first_name' => $request->parent_first_name,
            'parent_last_name' => $request->parent_last_name,
            'parent_phone' => $request->parent_phone,
            'parent_email' => $request->parent_email,
            'total_fees' => $amount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending'
        ];

        if ($isRenewal) {
            // Réinscription
            $student = Student::find($request->existing_student_id);
            $enrollmentData['student_id'] = $student->id;
            $enrollmentData['is_new_enrollment'] = false;
            $enrollmentData['applicant_first_name'] = $student->first_name;
            $enrollmentData['applicant_last_name'] = $student->last_name;
            $enrollmentData['applicant_date_of_birth'] = $student->date_of_birth;
            $enrollmentData['applicant_gender'] = $student->gender;
        } else {
            // Nouvelle inscription
            $enrollmentData['is_new_enrollment'] = true;
            $enrollmentData['applicant_first_name'] = $request->student_first_name;
            $enrollmentData['applicant_last_name'] = $request->student_last_name;
            $enrollmentData['applicant_date_of_birth'] = $request->student_date_of_birth;
            $enrollmentData['applicant_gender'] = $request->student_gender;
        }

        $enrollment = \App\Models\Enrollment::create($enrollmentData);

        // Créer le paiement en ligne
        $paymentData = [
            'transaction_id' => OnlinePayment::generateTransactionId(),
            'enrollment_id' => $enrollment->id,
            'amount' => $amount,
            'payment_type' => $isRenewal ? 're_enrollment' : 'enrollment',
            'payment_method' => $request->payment_method,
            'payer_name' => $request->parent_first_name . ' ' . $request->parent_last_name,
            'payer_phone' => $request->payer_phone,
            'payer_email' => $request->parent_email,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if ($isRenewal) {
            $paymentData['student_id'] = $student->id;
        }

        $payment = OnlinePayment::create($paymentData);

        return redirect()->route('parent-portal.payment', $payment->transaction_id);
    }

    /**
     * Afficher la page de paiement
     */
    public function showPayment($transactionId)
    {
        $payment = OnlinePayment::where('transaction_id', $transactionId)->firstOrFail();
        $gateway = PaymentGateway::byCode($payment->payment_method)->first();

        return view('parent-portal.payment', compact('payment', 'gateway'));
    }

    /**
     * Traiter le paiement
     */
    public function processPayment(Request $request, $transactionId)
    {
        $payment = OnlinePayment::where('transaction_id', $transactionId)->firstOrFail();
        
        // Simuler le traitement du paiement (en production, intégrer avec les vraies APIs)
        $payment->update([
            'status' => 'processing',
            'gateway_response' => json_encode($request->all())
        ]);

        // Simuler une réponse de succès après 2 secondes
        sleep(2);
        
        $payment->markAsCompleted('GATEWAY_' . strtoupper(Str::random(8)));

        // Mettre à jour l'inscription
        if ($payment->enrollment) {
            $payment->enrollment->update([
                'enrollment_status' => 'completed',
                'payment_status' => 'completed',
                'amount_paid' => $payment->amount,
                'receipt_number' => 'RCP' . date('Ymd') . strtoupper(Str::random(6))
            ]);
        }

        return redirect()->route('parent-portal.payment-success', $transactionId);
    }

    /**
     * Page de succès du paiement
     */
    public function paymentSuccess($transactionId)
    {
        $payment = OnlinePayment::where('transaction_id', $transactionId)->firstOrFail();
        
        return view('parent-portal.payment-success', compact('payment'));
    }

    /**
     * Obtenir le parent actuel
     */
    private function getCurrentParent()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'parent') {
            abort(401, 'Non authentifié ou accès non autorisé.');
        }

        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            abort(404, 'Profil parent non trouvé.');
        }

        return $parent;
    }
}
