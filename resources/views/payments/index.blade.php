@extends('layouts.app')

@section('title', 'Gestion des Paiements - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Paiements</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Paiements</h1>
                    <p class="text-muted">Enregistrez et suivez les paiements des frais scolaires</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="bi bi-credit-card me-2"></i>
                        Nouveau paiement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalRevenue ?? 2500000, 0, ',', ' ') }}</h4>
                            <span>Total revenus (FCFA)</span>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalPayments ?? 156 }}</h4>
                            <span>Paiements effectués</span>
                        </div>
                        <i class="bi bi-credit-card fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $pendingPayments ?? 23 }}</h4>
                            <span>En attente</span>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($monthlyRevenue ?? 450000, 0, ',', ' ') }}</h4>
                            <span>Ce mois (FCFA)</span>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-credit-card me-2"></i>
                        Historique des paiements
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-funnel"></i> Filtrer
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download"></i> Exporter
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>N° Reçu</th>
                                    <th>Élève</th>
                                    <th>Type de frais</th>
                                    <th>Montant</th>
                                    <th>Mode de paiement</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">PAY2025001</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/30x30/007bff/ffffff?text=KM" class="rounded-circle me-2" width="30" height="30">
                                            <span>Kouassi Marie</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Frais de scolarité</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">50,000 FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Espèces</span>
                                    </td>
                                    <td>
                                        <small>15/07/2025</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Confirmé</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPayment(1)" title="Voir reçu">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="printReceipt(1)" title="Imprimer reçu">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">PAY2025002</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/30x30/28a745/ffffff?text=BA" class="rounded-circle me-2" width="30" height="30">
                                            <span>Bamba Amadou</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Frais d'inscription</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">25,000 FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Virement</span>
                                    </td>
                                    <td>
                                        <small>12/07/2025</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">En attente</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPayment(2)" title="Voir reçu">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="confirmPayment(2)" title="Confirmer">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="bi bi-credit-card me-2"></i>
                    Enregistrer un nouveau paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informations du paiement</h6>
                            <div class="mb-3">
                                <label for="payment_id" class="form-label">N° de reçu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="payment_id" name="payment_id" placeholder="PAY2025003" required>
                            </div>
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Élève <span class="text-danger">*</span></label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Sélectionner un élève...</option>
                                    <option value="1">Kouassi Marie - CP1</option>
                                    <option value="2">Bamba Amadou - CE1</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fee_id" class="form-label">Type de frais <span class="text-danger">*</span></label>
                                <select class="form-select" id="fee_id" name="fee_id" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="1">Frais de scolarité - 50,000 FCFA</option>
                                    <option value="2">Frais d'inscription - 25,000 FCFA</option>
                                    <option value="3">Frais de transport - 15,000 FCFA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3">Détails du paiement</h6>
                            <div class="mb-3">
                                <label for="amount_paid" class="form-label">Montant payé <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="amount_paid" name="amount_paid" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="cash">Espèces</option>
                                    <option value="bank_transfer">Virement bancaire</option>
                                    <option value="check">Chèque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Date de paiement <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Remarques</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Remarques sur le paiement..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Enregistrer le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewPayment(id) {
    alert('Voir les détails du paiement #' + id);
}

function printReceipt(id) {
    alert('Impression du reçu #' + id);
}

function confirmPayment(id) {
    if (confirm('Confirmer ce paiement ?')) {
        alert('Paiement confirmé avec succès!');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Paiement enregistré avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('addPaymentModal')).hide();
    });
});
</script>
@endpush 