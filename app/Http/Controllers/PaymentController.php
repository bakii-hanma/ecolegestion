<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnlinePayment;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer les paiements avec les relations
        $payments = OnlinePayment::with(['enrollment.student', 'enrollment.schoolClass', 'enrollment.academicYear'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculer les statistiques
        $stats = $this->calculatePaymentStats();

        // Données pour les filtres
        $classes = SchoolClass::orderBy('name')->get();
        $students = Student::orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Données pour le modal "Nouveau paiement"
        $enrollments = Enrollment::with(['student', 'schoolClass', 'academicYear'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [
            'moov_money' => 'Moov Money',
            'airtel_money' => 'Airtel Money',
            'card' => 'Carte bancaire'
        ];

        return view('payments.index', compact(
            'payments', 'stats', 'classes', 'students', 'academicYears',
            'enrollments', 'paymentMethods'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $enrollments = Enrollment::with(['student', 'schoolClass', 'academicYear'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [
            'moov_money' => 'Moov Money',
            'airtel_money' => 'Airtel Money',
            'card' => 'Carte bancaire'
        ];

        return view('payments.create', compact('enrollments', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:moov_money,airtel_money,card',
            'payer_name' => 'required|string|max:255',
            'payer_phone' => 'required|string|max:20',
            'payer_email' => 'nullable|email',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Générer un ID de transaction unique
        $validated['transaction_id'] = OnlinePayment::generateTransactionId();
        
        // Définir le statut par défaut
        $validated['status'] = 'completed';
        $validated['payment_type'] = 'fees';

        $payment = OnlinePayment::create($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = OnlinePayment::with(['enrollment.student', 'enrollment.schoolClass', 'enrollment.academicYear'])
            ->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = OnlinePayment::findOrFail($id);
        $enrollments = Enrollment::with(['student', 'schoolClass', 'academicYear'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [
            'moov_money' => 'Moov Money',
            'airtel_money' => 'Airtel Money',
            'card' => 'Carte bancaire'
        ];

        return view('payments.edit', compact('payment', 'enrollments', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = OnlinePayment::findOrFail($id);

        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:moov_money,airtel_money,card',
            'payer_name' => 'required|string|max:255',
            'payer_phone' => 'required|string|max:20',
            'payer_email' => 'nullable|email',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = OnlinePayment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }

    /**
     * Calculer les statistiques des paiements
     */
    private function calculatePaymentStats()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return [
                'totalRevenue' => 0,
                'totalPayments' => 0,
                'pendingPayments' => 0,
                'monthlyRevenue' => 0
            ];
        }

        // Total des revenus de l'année académique
        $totalRevenue = OnlinePayment::whereHas('enrollment', function($query) use ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        })->sum('amount');

        // Nombre total de paiements
        $totalPayments = OnlinePayment::whereHas('enrollment', function($query) use ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        })->count();

        // Revenus du mois en cours
        $monthlyRevenue = OnlinePayment::whereHas('enrollment', function($query) use ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->sum('amount');

        // Paiements en attente (inscriptions sans paiement)
        $pendingPayments = Enrollment::where('academic_year_id', $currentYear->id)
            ->where('status', 'active')
            ->whereDoesntHave('onlinePayments')
            ->count();

        return [
            'totalRevenue' => $totalRevenue,
            'totalPayments' => $totalPayments,
            'pendingPayments' => $pendingPayments,
            'monthlyRevenue' => $monthlyRevenue
        ];
    }
}
