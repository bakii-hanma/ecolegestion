@extends('layouts.app')

@section('title', 'Gestion des Frais - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Frais scolaires</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Frais Scolaires</h1>
                    <p class="text-muted">Configurez et gérez les différents types de frais</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                        <i class="bi bi-cash-stack me-2"></i>
                        Nouveau frais
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fees Cards -->
    <div class="row mb-4">
        <!-- Frais de scolarité -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Frais de scolarité</h5>
                        <span class="badge bg-light text-dark">Mensuel</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h3 text-primary mb-0">50,000 FCFA</span>
                        <span class="badge bg-success">Obligatoire</span>
                    </div>
                    <div class="mb-2">
                        <strong>Classes concernées:</strong> Toutes
                    </div>
                    <div class="mb-2">
                        <strong>Échéance:</strong> Chaque 1er du mois
                    </div>
                    <div class="mb-3">
                        <strong>Année scolaire:</strong> 2024-2025
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Payé: 145/180</span>
                        <span>80.6%</span>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 80.6%"></div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewFee(1)">
                            <i class="bi bi-eye"></i> Voir
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editFee(1)">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewPayments(1)">
                            <i class="bi bi-list"></i> Paiements
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Frais d'inscription -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Frais d'inscription</h5>
                        <span class="badge bg-light text-dark">Unique</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h3 text-warning mb-0">25,000 FCFA</span>
                        <span class="badge bg-success">Obligatoire</span>
                    </div>
                    <div class="mb-2">
                        <strong>Classes concernées:</strong> Toutes
                    </div>
                    <div class="mb-2">
                        <strong>Échéance:</strong> À l'inscription
                    </div>
                    <div class="mb-3">
                        <strong>Année scolaire:</strong> 2024-2025
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Payé: 178/180</span>
                        <span>98.9%</span>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 98.9%"></div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewFee(2)">
                            <i class="bi bi-eye"></i> Voir
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editFee(2)">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewPayments(2)">
                            <i class="bi bi-list"></i> Paiements
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Frais de transport -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Frais de transport</h5>
                        <span class="badge bg-light text-dark">Mensuel</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h3 text-info mb-0">15,000 FCFA</span>
                        <span class="badge bg-secondary">Optionnel</span>
                    </div>
                    <div class="mb-2">
                        <strong>Classes concernées:</strong> Toutes
                    </div>
                    <div class="mb-2">
                        <strong>Échéance:</strong> Chaque 1er du mois
                    </div>
                    <div class="mb-3">
                        <strong>Année scolaire:</strong> 2024-2025
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Payé: 89/180</span>
                        <span>49.4%</span>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 49.4%"></div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewFee(3)">
                            <i class="bi bi-eye"></i> Voir
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editFee(3)">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewPayments(3)">
                            <i class="bi bi-list"></i> Paiements
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add new fee card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-dashed" style="border: 2px dashed #dee2e6;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <i class="bi bi-plus-circle fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Ajouter un nouveau frais</h5>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                        <i class="bi bi-plus me-2"></i>
                        Créer un frais
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>
                        Récapitulatif des frais
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-funnel"></i> Filtrer
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download"></i> Rapport
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type de frais</th>
                                    <th>Montant</th>
                                    <th>Fréquence</th>
                                    <th>Classes</th>
                                    <th>Statut</th>
                                    <th>Taux de paiement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-mortarboard text-primary me-2"></i>
                                            <span class="fw-bold">Frais de scolarité</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">50,000 FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Mensuel</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Toutes</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Actif</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 80px; height: 8px;">
                                                <div class="progress-bar bg-success" style="width: 80.6%"></div>
                                            </div>
                                            <small>80.6%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewFee(1)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editFee(1)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFee(1)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clipboard-check text-warning me-2"></i>
                                            <span class="fw-bold">Frais d'inscription</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">25,000 FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Unique</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Toutes</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Actif</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 80px; height: 8px;">
                                                <div class="progress-bar bg-success" style="width: 98.9%"></div>
                                            </div>
                                            <small>98.9%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewFee(2)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editFee(2)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFee(2)">
                                                <i class="bi bi-trash"></i>
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

<!-- Add Fee Modal -->
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-labelledby="addFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFeeModalLabel">
                    <i class="bi bi-cash-stack me-2"></i>
                    Créer un nouveau frais
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFeeForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informations du frais</h6>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du frais <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ex: Frais de cantine" required>
                            </div>
                            <div class="mb-3">
                                <label for="fee_type" class="form-label">Type de frais <span class="text-danger">*</span></label>
                                <select class="form-select" id="fee_type" name="fee_type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="tuition">Frais de scolarité</option>
                                    <option value="registration">Frais d'inscription</option>
                                    <option value="uniform">Uniforme</option>
                                    <option value="transport">Transport</option>
                                    <option value="meal">Cantine</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Montant <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="amount" name="amount" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3">Configuration</h6>
                            <div class="mb-3">
                                <label for="frequency" class="form-label">Fréquence <span class="text-danger">*</span></label>
                                <select class="form-select" id="frequency" name="frequency" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="monthly">Mensuel</option>
                                    <option value="quarterly">Trimestriel</option>
                                    <option value="yearly">Annuel</option>
                                    <option value="one_time">Unique</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Classes concernées</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">Toutes les classes</option>
                                    <option value="1">CP1</option>
                                    <option value="2">CE1</option>
                                    <option value="3">CM1</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Date d'échéance</label>
                                <input type="date" class="form-control" id="due_date" name="due_date">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" checked>
                                <label class="form-check-label" for="is_mandatory">
                                    Frais obligatoire
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Frais actif
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description détaillée du frais..."></textarea>
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
                        Créer le frais
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewFee(id) {
    alert('Voir les détails du frais #' + id);
}

function editFee(id) {
    alert('Modifier le frais #' + id);
}

function deleteFee(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce frais ?')) {
        alert('Frais supprimé avec succès!');
    }
}

function viewPayments(id) {
    alert('Voir les paiements pour le frais #' + id);
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addFeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Frais créé avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('addFeeModal')).hide();
    });
});
</script>
@endpush 