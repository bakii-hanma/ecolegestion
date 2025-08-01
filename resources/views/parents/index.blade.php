@extends('layouts.app')

@section('title', 'Gestion des Parents - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Parents</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Parents</h1>
                    <p class="text-muted">Gérez les informations des parents et tuteurs</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParentModal">
                        <i class="bi bi-people-fill me-2"></i>
                        Ajouter un parent
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalParents ?? 156 }}</h4>
                            <span>Total parents</span>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $activeContacts ?? 145 }}</h4>
                            <span>Contacts actifs</span>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $primaryContacts ?? 98 }}</h4>
                            <span>Contacts principaux</span>
                        </div>
                        <i class="bi bi-star-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $canPickup ?? 134 }}</h4>
                            <span>Autorisés récupération</span>
                        </div>
                        <i class="bi bi-shield-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom, prénom, téléphone..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Relation</label>
                            <select class="form-select" id="relationFilter">
                                <option value="">Toutes</option>
                                <option value="father">Père</option>
                                <option value="mother">Mère</option>
                                <option value="guardian">Tuteur</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Contact principal</label>
                            <select class="form-select" id="primaryFilter">
                                <option value="">Tous</option>
                                <option value="1">Oui</option>
                                <option value="0">Non</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Récupération</label>
                            <select class="form-select" id="pickupFilter">
                                <option value="">Tous</option>
                                <option value="1">Autorisé</option>
                                <option value="0">Non autorisé</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success w-100" onclick="exportData()">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Liste des parents
                    </h5>
                    <span class="badge bg-primary fs-6">{{ $parentsCount ?? 156 }} parents</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Parent</th>
                                    <th>Relation</th>
                                    <th>Enfants</th>
                                    <th>Contact</th>
                                    <th>Profession</th>
                                    <th>Autorisations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40x40/007bff/ffffff?text=KJ" class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <div class="fw-bold">Kouassi Jean</div>
                                                <small class="text-muted">Père</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Père</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info me-1">Kouassi Marie</span>
                                        <span class="badge bg-secondary">1 enfant</span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="d-block">+225 07 00 00 00</small>
                                            <small class="text-muted">jean.kouassi@email.com</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold small">Ingénieur</div>
                                            <small class="text-muted">Orange CI</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success me-1">Contact principal</span>
                                        <span class="badge bg-info">Récupération</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewParent(1)" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editParent(1)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteParent(1)" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40x40/28a745/ffffff?text=BF" class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <div class="fw-bold">Bamba Fatou</div>
                                                <small class="text-muted">Mère</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Mère</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info me-1">Bamba Amadou</span>
                                        <span class="badge bg-secondary">1 enfant</span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="d-block">+225 05 11 22 33</small>
                                            <small class="text-muted">fatou.bamba@email.com</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold small">Commerçante</div>
                                            <small class="text-muted">Marché de Cocody</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success me-1">Contact principal</span>
                                        <span class="badge bg-info">Récupération</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewParent(2)" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editParent(2)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteParent(2)" title="Supprimer">
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

<!-- Add Parent Modal -->
<div class="modal fade" id="addParentModal" tabindex="-1" aria-labelledby="addParentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParentModalLabel">
                    <i class="bi bi-people-fill me-2"></i>
                    Ajouter un nouveau parent
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addParentForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informations personnelles</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone_2" class="form-label">Téléphone 2</label>
                                <input type="tel" class="form-control" id="phone_2" name="phone_2">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3">Informations professionnelles</h6>
                            <div class="mb-3">
                                <label for="profession" class="form-label">Profession</label>
                                <input type="text" class="form-control" id="profession" name="profession">
                            </div>
                            <div class="mb-3">
                                <label for="workplace" class="form-label">Lieu de travail</label>
                                <input type="text" class="form-control" id="workplace" name="workplace">
                            </div>
                            <div class="mb-3">
                                <label for="relationship" class="form-label">Relation <span class="text-danger">*</span></label>
                                <select class="form-select" id="relationship" name="relationship" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="father">Père</option>
                                    <option value="mother">Mère</option>
                                    <option value="guardian">Tuteur</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_primary_contact" name="is_primary_contact">
                                <label class="form-check-label" for="is_primary_contact">
                                    Contact principal
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="can_pickup" name="can_pickup" checked>
                                <label class="form-check-label" for="can_pickup">
                                    Autorisé à récupérer l'enfant
                                </label>
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
                        Enregistrer le parent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewParent(id) {
    alert('Voir les détails du parent #' + id);
}

function editParent(id) {
    alert('Modifier le parent #' + id);
}

function deleteParent(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce parent ?')) {
        alert('Parent supprimé avec succès!');
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('relationFilter').value = '';
    document.getElementById('primaryFilter').value = '';
    document.getElementById('pickupFilter').value = '';
}

function exportData() {
    alert('Export en cours...');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addParentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Parent ajouté avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('addParentModal')).hide();
    });
});
</script>
@endpush 