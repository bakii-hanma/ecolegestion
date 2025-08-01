@extends('layouts.app')

@section('title', 'Gestion des Présences - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Présences</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Présences</h1>
                    <p class="text-muted">Marquez et suivez les présences quotidiennes</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                        <i class="bi bi-calendar-check me-2"></i>
                        Marquer les présences
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $todayPresent ?? 92 }}%</h4>
                            <span>Présents aujourd'hui</span>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $todayAbsent ?? 8 }}%</h4>
                            <span>Absents aujourd'hui</span>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $todayLate ?? 5 }}</h4>
                            <span>Retards aujourd'hui</span>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($weeklyAverage ?? 89.5, 1) }}%</h4>
                            <span>Moyenne semaine</span>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date and Class Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="attendanceDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Classe</label>
                            <select class="form-select" id="classSelect">
                                <option value="">Sélectionner une classe</option>
                                <option value="cp1">CP1</option>
                                <option value="ce1">CE1</option>
                                <option value="cm1">CM1</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="present">Présents</option>
                                <option value="absent">Absents</option>
                                <option value="late">Retards</option>
                                <option value="excused">Excusés</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary w-100" onclick="loadAttendance()">
                                    <i class="bi bi-search"></i> Charger
                                </button>
                                <button type="button" class="btn btn-outline-success w-100" onclick="exportAttendance()">
                                    <i class="bi bi-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Présences du {{ date('d/m/Y') }}
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="markAllPresent()">
                            <i class="bi bi-check-all"></i> Tous présents
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveAttendance()">
                            <i class="bi bi-save"></i> Sauvegarder
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th width="15%">Statut</th>
                                    <th width="15%">Heure d'arrivée</th>
                                    <th width="25%">Remarques</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
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
                                        <select class="form-select form-select-sm status-select" data-student="1">
                                            <option value="present" selected>Présent</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Retard</option>
                                            <option value="excused">Excusé</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" value="08:00" data-student="1">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" placeholder="Remarques..." data-student="1">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="viewHistory(1)" title="Historique">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="2">
                                        </div>
                                    </td>
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
                                        <select class="form-select form-select-sm status-select" data-student="2">
                                            <option value="present">Présent</option>
                                            <option value="absent" selected>Absent</option>
                                            <option value="late">Retard</option>
                                            <option value="excused">Excusé</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" data-student="2" disabled>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" placeholder="Raison de l'absence..." data-student="2">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="viewHistory(2)" title="Historique">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <span class="text-success">● Présents: 1</span> |
                                <span class="text-danger">● Absents: 1</span> |
                                <span class="text-warning">● Retards: 0</span>
                            </small>
                        </div>
                        <button type="button" class="btn btn-success" onclick="saveAttendance()">
                            <i class="bi bi-save me-2"></i>
                            Sauvegarder les présences
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markAttendanceModalLabel">
                    <i class="bi bi-calendar-check me-2"></i>
                    Marquer les présences
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modal_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="modal_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label for="modal_class" class="form-label">Classe <span class="text-danger">*</span></label>
                    <select class="form-select" id="modal_class" required>
                        <option value="">Sélectionner une classe...</option>
                        <option value="cp1">CP1</option>
                        <option value="ce1">CE1</option>
                        <option value="cm1">CM1</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Sélectionnez la date et la classe pour commencer à marquer les présences.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" onclick="startAttendance()">
                    <i class="bi bi-check-circle me-2"></i>
                    Commencer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const classId = document.getElementById('classSelect').value;
    
    if (!date || !classId) {
        alert('Veuillez sélectionner une date et une classe');
        return;
    }
    
    alert('Chargement des présences pour ' + classId + ' du ' + date);
}

function markAllPresent() {
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.value = 'present';
        select.dispatchEvent(new Event('change'));
    });
}

function saveAttendance() {
    alert('Présences sauvegardées avec succès!');
}

function viewHistory(studentId) {
    alert('Voir l\'historique des présences de l\'élève #' + studentId);
}

function startAttendance() {
    const date = document.getElementById('modal_date').value;
    const classId = document.getElementById('modal_class').value;
    
    if (!date || !classId) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    alert('Démarrage de la prise de présences pour ' + classId);
    bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
}

function exportAttendance() {
    alert('Export des présences en cours...');
}

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des changements de statut
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-select')) {
            const studentId = e.target.dataset.student;
            const timeInput = document.querySelector(`input[type="time"][data-student="${studentId}"]`);
            
            if (e.target.value === 'absent' || e.target.value === 'excused') {
                timeInput.disabled = true;
                timeInput.value = '';
            } else {
                timeInput.disabled = false;
                if (!timeInput.value) {
                    timeInput.value = '08:00';
                }
            }
        }
    });

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
@endpush 