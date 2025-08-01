@extends('layouts.app')

@section('title', 'Gestion des Élèves - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Élèves</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Élèves</h1>
                    <p class="text-muted">Gérez les informations de tous les élèves de l'établissement</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus me-2"></i>
                        Ajouter un élève
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
                            <h4 class="mb-0">{{ $totalStudents ?? 0 }}</h4>
                            <span>Total élèves</span>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $activeStudents ?? 0 }}</h4>
                            <span>Actifs</span>
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
                            <h4 class="mb-0">{{ $newThisMonth ?? 0 }}</h4>
                            <span>Nouveaux</span>
                        </div>
                        <i class="bi bi-person-plus fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $studentsByCycle['college'] ?? 0 }}</h4>
                            <span>Collège</span>
                        </div>
                        <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom, prénom, matricule..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cycle</label>
                            <select class="form-select" id="cycleFilter">
                                <option value="">Tous les cycles</option>
                                <option value="preprimaire">Pré-primaire</option>
                                <option value="primaire">Primaire</option>
                                <option value="college">Collège</option>
                                <option value="lycee">Lycée</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Niveau</label>
                            <select class="form-select" id="levelFilter">
                                <option value="">Tous les niveaux</option>
                                @foreach($levels ?? [] as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Classe</label>
                            <select class="form-select" id="classFilter">
                                <option value="">Toutes les classes</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="graduated">Diplômé</option>
                                <option value="transferred">Transféré</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Liste des élèves
                    </h5>
                    <span class="badge bg-primary fs-6" id="studentCount">{{ $students->count() ?? 0 }} élèves</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="studentsTable">
                            <thead style="background: linear-gradient(135deg, #007bff, #0056b3); color: white;">
                                <tr>
                                    <th class="border-0" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="border-0" style="width: 60px;">Photo</th>
                                    <th class="border-0" style="width: 100px;">Matricule</th>
                                    <th class="border-0" style="min-width: 180px;">Nom complet</th>
                                    <th class="border-0" style="width: 120px;">Classe actuelle</th>
                                    <th class="border-0" style="width: 60px;">Âge</th>
                                    <th class="border-0" style="width: 160px;">Contact</th>
                                    <th class="border-0" style="width: 90px;">Statut</th>
                                    <th class="border-0" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $student->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}" class="rounded-circle" width="40" height="40" alt="Photo">
                                            @else
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-primary rounded-circle" style="width: 40px; height: 40px; font-size: 1rem;">
                                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $student->student_id }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $student->full_name }}</div>
                                            <small class="text-muted">Né(e) le {{ $student->date_of_birth->format('d/m/Y') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php 
                                            $currentClass = $student->getCurrentClass(); 
                                            $currentLevel = $student->getCurrentLevel();
                                        @endphp
                                        @if($currentClass)
                                            <span class="badge bg-info">{{ $currentClass->name ?? 'Nom manquant' }}</span>
                                            @if($currentLevel && $currentLevel->name)
                                                <br><small class="text-muted">{{ $currentLevel->name }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Non inscrit</span>
                                        @endif
                                    </td>
                                    <td>{{ $student->age }} ans</td>
                                    <td>
                                        <div>
                                            @if($student->parents->isNotEmpty())
                                                <small class="text-muted d-block">Parent: {{ $student->parents->first()->first_name }} {{ $student->parents->first()->last_name }}</small>
                                                <small class="text-muted">{{ $student->parents->first()->phone }}</small>
                                            @else
                                                <small class="text-warning">Aucun parent enregistré</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($student->status === 'inactive')
                                            <span class="badge bg-secondary">Inactif</span>
                                        @elseif($student->status === 'graduated')
                                            <span class="badge bg-primary">Diplômé</span>
                                        @else
                                            <span class="badge bg-warning">Transféré</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" data-student-id="{{ $student->id }}" onclick="viewStudent(this.dataset.studentId)" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-student-id="{{ $student->id }}" onclick="editStudent(this.dataset.studentId)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-student-id="{{ $student->id }}" onclick="deleteStudent(this.dataset.studentId)" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                            <h5>Aucun élève trouvé</h5>
                                            <p>Commencez par ajouter un élève en cliquant sur le bouton ci-dessus.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($students->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves
                        </div>
                        <nav aria-label="Pagination des élèves">
                            {{ $students->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Ajouter un nouvel élève
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStudentForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- Informations personnelles -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-person me-2"></i>
                                Informations personnelles
                            </h6>
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Matricule <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Ex: STU2024003" required>
                            </div>
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
                                    <label for="date_of_birth" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Sexe <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="male">Masculin</option>
                                        <option value="female">Féminin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="place_of_birth" class="form-label">Lieu de naissance</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Informations scolaires -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="bi bi-mortarboard me-2"></i>
                                Informations scolaires
                            </h6>
                            <div class="mb-3">
                                <label for="cycle" class="form-label">Cycle <span class="text-danger">*</span></label>
                                <select class="form-select" id="cycle" name="cycle" required>
                                    <option value="">Sélectionner un cycle</option>
                                    <option value="preprimaire">Pré-primaire</option>
                                    <option value="primaire">Primaire</option>
                                    <option value="college">Collège</option>
                                    <option value="lycee">Lycée</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="level_id" class="form-label">Niveau <span class="text-danger">*</span></label>
                                <select class="form-select" id="level_id" name="level_id" required>
                                    <option value="">Sélectionner un niveau</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Classe <span class="text-danger">*</span></label>
                                <select class="form-select" id="class_id" name="class_id" required>
                                    <option value="">Sélectionner une classe</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="academic_year_id" class="form-label">Année scolaire <span class="text-danger">*</span></label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Sélectionner une année</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="enrollment_date" class="form-label">Date d'inscription <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="emergency_contact" class="form-label">Contact d'urgence</label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" placeholder="+225 XX XX XX XX">
                            </div>
                            <div class="mb-3">
                                <label for="medical_conditions" class="form-label">Conditions médicales</label>
                                <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="3" placeholder="Allergies, conditions particulières..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo de l'élève</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
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
                        Enregistrer l'élève
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewStudentModalLabel">
                    <i class="bi bi-person-circle me-2"></i>
                    Détails de l'élève
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="studentDetails">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="printStudentCard()">
                    <i class="bi bi-printer me-2"></i>
                    Imprimer la fiche
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    const cycleSelect = document.getElementById('cycle');
    const levelSelect = document.getElementById('level_id');
    const classSelect = document.getElementById('class_id');

    // Chargement des niveaux par cycle
    if (cycleSelect) {
        cycleSelect.addEventListener('change', function() {
            const cycle = this.value;
            levelSelect.innerHTML = '<option value="">Sélectionner un niveau</option>';
            classSelect.innerHTML = '<option value="">Sélectionner une classe</option>';
            
            if (cycle) {
                // Charger les niveaux par AJAX
                fetch(`/api/levels-by-cycle?cycle=${cycle}`)
                    .then(response => response.json())
                    .then(levels => {
                        levels.forEach(level => {
                            levelSelect.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                        });
                    });
            }
        });
    }

    // Chargement des classes par niveau
    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            const levelId = this.value;
            classSelect.innerHTML = '<option value="">Sélectionner une classe</option>';
            
            if (levelId) {
                fetch(`/api/students/classes-by-level?level_id=${levelId}`)
                    .then(response => response.json())
                    .then(classes => {
                        classes.forEach(classItem => {
                            classSelect.innerHTML += `<option value="${classItem.id}">${classItem.name}</option>`;
                        });
                    });
            }
        });
    }

    // Form submission
    document.getElementById('addStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Ici, vous ajouterez la logique pour envoyer les données au serveur
        alert('Élève ajouté avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterStudents();
    });

    // Filter functionality
    document.getElementById('cycleFilter').addEventListener('change', filterStudents);
    document.getElementById('levelFilter').addEventListener('change', filterStudents);
    document.getElementById('classFilter').addEventListener('change', filterStudents);
    document.getElementById('statusFilter').addEventListener('change', filterStudents);

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

function filterStudents() {
    // Implémentation du filtrage des élèves
    console.log('Filtrage des élèves...');
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('cycleFilter').value = '';
    document.getElementById('levelFilter').value = '';
    document.getElementById('classFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterStudents();
}

function exportData() {
    // Implémentation de l'export des données
    alert('Export en cours...');
}

function viewStudent(id) {
    // Charger les détails de l'élève
    console.log('Affichage des détails de l\'élève ID:', id);
    new bootstrap.Modal(document.getElementById('viewStudentModal')).show();
}

function editStudent(id) {
    // Ouvrir le modal d'édition avec les données pré-remplies
    alert('Fonction d\'édition à implémenter');
}

function deleteStudent(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')) {
        // Implémentation de la suppression
        alert('Élève supprimé avec succès!');
    }
}

function printStudentCard() {
    window.print();
}
</script>
@endpush 