@extends('layouts.app')

@section('title', 'Gestion des Notes - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Notes</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Notes</h1>
                    <p class="text-muted">Saisissez et consultez les notes des élèves</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                        <i class="bi bi-journal-plus me-2"></i>
                        Saisir des notes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalGrades ?? 1250 }}</h4>
                            <span>Notes saisies</span>
                        </div>
                        <i class="bi bi-journal-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($averageGrade ?? 14.5, 1) }}/20</h4>
                            <span>Moyenne générale</span>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $pendingGrades ?? 45 }}</h4>
                            <span>En attente de saisie</span>
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
                            <h4 class="mb-0">{{ $completionRate ?? 92 }}%</h4>
                            <span>Taux de complétion</span>
                        </div>
                        <i class="bi bi-check-all fs-1 opacity-50"></i>
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
                        <div class="col-md-2">
                            <label class="form-label">Classe</label>
                            <select class="form-select" id="classFilter">
                                <option value="">Toutes les classes</option>
                                <option value="cp1">CP1</option>
                                <option value="ce1">CE1</option>
                                <option value="cm1">CM1</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Matière</label>
                            <select class="form-select" id="subjectFilter">
                                <option value="">Toutes les matières</option>
                                <option value="mathematiques">Mathématiques</option>
                                <option value="francais">Français</option>
                                <option value="sciences">Sciences</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type d'évaluation</label>
                            <select class="form-select" id="examTypeFilter">
                                <option value="">Tous les types</option>
                                <option value="devoir">Devoir</option>
                                <option value="composition">Composition</option>
                                <option value="controle">Contrôle</option>
                                <option value="oral">Oral</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trimestre</label>
                            <select class="form-select" id="termFilter">
                                <option value="">Tous</option>
                                <option value="1er trimestre" selected>1er trimestre</option>
                                <option value="2ème trimestre">2ème trimestre</option>
                                <option value="3ème trimestre">3ème trimestre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Enseignant</label>
                            <select class="form-select" id="teacherFilter">
                                <option value="">Tous</option>
                                <option value="1">Mme. Adjoua Koffi</option>
                                <option value="2">M. Konan Yao</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success w-100" onclick="exportGrades()">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-journal-text me-2"></i>
                        Relevé de notes
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleView('table')">
                            <i class="bi bi-table"></i> Tableau
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleView('cards')">
                            <i class="bi bi-grid"></i> Cartes
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="gradesTable">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Date évaluation</th>
                                    <th>Enseignant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/30x30/007bff/ffffff?text=KM" class="rounded-circle me-2" width="30" height="30">
                                            <span class="fw-bold">Kouassi Marie</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">CP1</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Mathématiques</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Devoir</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="h6 mb-0 me-2 text-success">16/20</span>
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-success" style="width: 80%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>15/07/2025</small>
                                    </td>
                                    <td>
                                        <small>Mme. Adjoua Koffi</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewGrade(1)" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editGrade(1)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteGrade(1)" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/30x30/28a745/ffffff?text=BA" class="rounded-circle me-2" width="30" height="30">
                                            <span class="fw-bold">Bamba Amadou</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">CE1</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Français</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Composition</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="h6 mb-0 me-2 text-warning">12/20</span>
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-warning" style="width: 60%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>12/07/2025</small>
                                    </td>
                                    <td>
                                        <small>M. Konan Yao</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewGrade(2)" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editGrade(2)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteGrade(2)" title="Supprimer">
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

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1" aria-labelledby="addGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGradeModalLabel">
                    <i class="bi bi-journal-plus me-2"></i>
                    Saisir des notes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addGradeForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informations de l'évaluation</h6>
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Élève <span class="text-danger">*</span></label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Sélectionner un élève...</option>
                                    <option value="1">Kouassi Marie - CP1</option>
                                    <option value="2">Bamba Amadou - CE1</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">Matière <span class="text-danger">*</span></label>
                                <select class="form-select" id="subject_id" name="subject_id" required>
                                    <option value="">Sélectionner une matière...</option>
                                    <option value="1">Mathématiques</option>
                                    <option value="2">Français</option>
                                    <option value="3">Sciences</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exam_type" class="form-label">Type d'évaluation <span class="text-danger">*</span></label>
                                <select class="form-select" id="exam_type" name="exam_type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="devoir">Devoir</option>
                                    <option value="composition">Composition</option>
                                    <option value="controle">Contrôle</option>
                                    <option value="oral">Oral</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="term" class="form-label">Trimestre <span class="text-danger">*</span></label>
                                <select class="form-select" id="term" name="term" required>
                                    <option value="1er trimestre" selected>1er trimestre</option>
                                    <option value="2ème trimestre">2ème trimestre</option>
                                    <option value="3ème trimestre">3ème trimestre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3">Note et détails</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="score" class="form-label">Note obtenue <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="score" name="score" min="0" max="20" step="0.5" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="max_score" class="form-label">Note maximale</label>
                                    <input type="number" class="form-control" id="max_score" name="max_score" value="20" min="1" max="100">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exam_date" class="form-label">Date de l'évaluation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="exam_date" name="exam_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="comments" class="form-label">Commentaires</label>
                                <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Commentaires sur la performance de l'élève..."></textarea>
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
                        Enregistrer la note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewGrade(id) {
    alert('Voir les détails de la note #' + id);
}

function editGrade(id) {
    alert('Modifier la note #' + id);
}

function deleteGrade(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
        alert('Note supprimée avec succès!');
    }
}

function resetFilters() {
    document.getElementById('classFilter').value = '';
    document.getElementById('subjectFilter').value = '';
    document.getElementById('examTypeFilter').value = '';
    document.getElementById('termFilter').value = '1er trimestre';
    document.getElementById('teacherFilter').value = '';
}

function exportGrades() {
    alert('Export des notes en cours...');
}

function toggleView(type) {
    if (type === 'cards') {
        alert('Vue en cartes à implémenter');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addGradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Note enregistrée avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('addGradeModal')).hide();
    });
});
</script>
@endpush 