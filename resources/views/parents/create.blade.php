@extends('layouts.app')

@section('title', 'Ajouter un Parent')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Ajouter un Parent
                        @if(isset($fromContext) && $fromContext)
                            @if($fromContext === 'enrollment')
                                <span class="badge bg-info ms-2">Suite à inscription</span>
                            @elseif($fromContext === 'student')
                                <span class="badge bg-success ms-2">Suite à création d'élève</span>
                            @endif
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        @if(isset($fromContext) && $fromContext)
                            @if($fromContext === 'enrollment')
                                Créer le parent pour l'élève nouvellement inscrit
                            @elseif($fromContext === 'student')
                                Créer le parent pour l'élève nouvellement créé
                            @endif
                        @else
                            Créer un nouveau parent et l'associer à ses enfants
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-edit me-2"></i>
                        Informations du Parent
                    </h6>
                </div>
                <div class="card-body">
                    <form id="parentForm" method="POST" action="{{ route('parents.store') }}">
                        @csrf
                        
                        <!-- Informations personnelles -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="first_name" class="form-label">
                                        <i class="fas fa-user me-1"></i>Prénom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="last_name" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars me-1"></i>Genre <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender" 
                                            required>
                                        <option value="">Sélectionner le genre</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Téléphone Principal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone_2" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Téléphone Secondaire
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone_2') is-invalid @enderror" 
                                           id="phone_2" 
                                           name="phone_2" 
                                           value="{{ old('phone_2') }}">
                                    @error('phone_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="relationship" class="form-label">
                                        <i class="fas fa-heart me-1"></i>Relation <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('relationship') is-invalid @enderror" 
                                            id="relationship" 
                                            name="relationship" 
                                            required>
                                        <option value="">Sélectionner la relation</option>
                                        <option value="father" {{ old('relationship') == 'father' ? 'selected' : '' }}>Père</option>
                                        <option value="mother" {{ old('relationship') == 'mother' ? 'selected' : '' }}>Mère</option>
                                        <option value="guardian" {{ old('relationship') == 'guardian' ? 'selected' : '' }}>Tuteur/Tutrice</option>
                                        <option value="other" {{ old('relationship') == 'other' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="profession" class="form-label">
                                        <i class="fas fa-briefcase me-1"></i>Profession
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('profession') is-invalid @enderror" 
                                           id="profession" 
                                           name="profession" 
                                           value="{{ old('profession') }}">
                                    @error('profession')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="workplace" class="form-label">
                                        <i class="fas fa-building me-1"></i>Lieu de Travail
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('workplace') is-invalid @enderror" 
                                           id="workplace" 
                                           name="workplace" 
                                           value="{{ old('workplace') }}">
                                    @error('workplace')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>Adresse
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Options supplémentaires -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_primary_contact" 
                                           name="is_primary_contact" 
                                           value="1" 
                                           {{ old('is_primary_contact') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_primary_contact">
                                        <i class="fas fa-star me-1"></i>Contact Principal
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="can_pickup" 
                                           name="can_pickup" 
                                           value="1" 
                                           {{ old('can_pickup', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="can_pickup">
                                        <i class="fas fa-user-check me-1"></i>Peut récupérer l'enfant
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section des étudiants -->
                        <div class="card mt-4">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Enfants associés <span class="text-white">*</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Recherche d'étudiant -->
                                <div class="form-group mb-3">
                                    <label for="student_search" class="form-label">
                                        <i class="fas fa-search me-1"></i>Rechercher un étudiant par matricule
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="student_search" 
                                               placeholder="Tapez le matricule de l'étudiant (ex: STU2024001)"
                                               autocomplete="off">
                                        <button type="button" class="btn btn-success" id="add_student_btn">
                                            <i class="fas fa-plus me-1"></i>Ajouter
                                        </button>
                                    </div>
                                    <div id="student_suggestions" class="dropdown-menu" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
                                </div>

                                <!-- Liste des étudiants sélectionnés -->
                                <div id="selected_students_container">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-users me-1"></i>Étudiants sélectionnés :
                                    </h6>
                                    <div id="selected_students_list" class="row">
                                        <!-- Les étudiants sélectionnés apparaîtront ici -->
                                    </div>
                                    <div id="no_students_message" class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Aucun étudiant sélectionné. Veuillez ajouter au moins un étudiant.
                                    </div>
                                </div>

                                <!-- Champs cachés pour les IDs des étudiants -->
                                <div id="student_ids_container">
                                    <!-- Les inputs cachés seront ajoutés ici dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('parents.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submit_btn">
                                        <i class="fas fa-save me-1"></i>Enregistrer le Parent
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations d'aide -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Guide d'utilisation
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Instructions permanentes -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="m-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Comment ajouter des étudiants
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <ol class="mb-0 small">
                                <li class="mb-1">Tapez le matricule de l'étudiant dans le champ de recherche</li>
                                <li class="mb-1">Sélectionnez l'étudiant dans la liste déroulante</li>
                                <li class="mb-1">Cliquez sur le bouton "+" pour l'ajouter</li>
                                <li class="mb-1">Répétez pour ajouter plusieurs enfants</li>
                                <li class="mb-0">Vous pouvez supprimer un étudiant avec le bouton "×"</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Règles importantes -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="m-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Règles importantes
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="mb-0 small">
                                <li class="mb-1">Au moins un étudiant doit être associé au parent</li>
                                <li class="mb-1">Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires</li>
                                <li class="mb-1">Un seul contact principal par famille est recommandé</li>
                                <li class="mb-1"><i class="fas fa-key text-primary me-1"></i><strong>Mot de passe généré automatiquement :</strong> Numéro de téléphone + 1234</li>
                                <li class="mb-1"><small class="text-muted"><i class="fas fa-info-circle me-1"></i>Exemple : Si le téléphone est 076527007, le mot de passe sera <code>0765270071234</code></small></li>
                                <li class="mb-0"><small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>L'email doit être unique - vérifiez qu'il n'existe pas déjà</small></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section d'aide dynamique -->
                    <div class="card shadow-sm mt-3" id="help-section">
                        <div class="card-header py-2 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-question-circle me-2"></i>
                                État actuel
                            </h6>
                        </div>
                        <div class="card-body p-3" id="help-content">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                Remplissez les informations du parent et ajoutez ses enfants.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS -->
<style>
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: white;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.student-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #f8f9fa;
    position: relative;
}

.student-card .remove-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
}

.student-card .remove-btn:hover {
    background: #c82333;
}

#no_students_message {
    display: block;
}

.has-students #no_students_message {
    display: none;
}

#help-section {
    transition: transform 0.2s ease-in-out, border-color 0.3s ease-in-out;
}

#help-content {
    transition: color 0.3s ease-in-out;
}

/* Forcer les bonnes couleurs pour les en-têtes */
.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df, #224abe) !important;
    color: white !important;
}

.bg-success {
    background-color: #28a745 !important;
    color: white !important;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.bg-info {
    background-color: #17a2b8 !important;
    color: white !important;
}

.bg-primary {
    background-color: #007bff !important;
    color: white !important;
}

/* S'assurer que le texte dans les en-têtes est bien blanc */
.card-header h6,
.card-header h6 i,
.card-header * {
    color: white !important;
}

/* Mais garder le texte du contenu des cartes lisible */
.card-body {
    color: #495057 !important;
}

.card-body * {
    color: inherit !important;
}

/* Forcer la couleur des en-têtes même avec Bootstrap */
.card-header.bg-gradient-primary,
.card-header.bg-success,
.card-header.bg-danger,
.card-header.bg-info,
.card-header.bg-primary {
    border: none !important;
}

/* ÉLIMINER COMPLÈTEMENT LE GRIS CLAIR DE BOOTSTRAP */
.card-header,
.card-header.bg-light,
.bg-light,
.card-header.py-3,
.card-header.py-2 {
    background-color: transparent !important;
    background-image: none !important;
    background: none !important;
}

/* Forcer les couleurs spécifiques sur TOUS les en-têtes */
.card-header.bg-gradient-primary,
.card-header.bg-primary {
    background: #007bff !important;
    background-color: #007bff !important;
    background-image: none !important;
    color: white !important;
}

.card-header.bg-success {
    background-color: #28a745 !important;
    background-image: none !important;
}

.card-header.bg-danger {
    background-color: #dc3545 !important;
    background-image: none !important;
}

.card-header.bg-info {
    background-color: #17a2b8 !important;
    background-image: none !important;
}

/* Forcer TOUS les en-têtes de cartes à être bleus */
.card .card-header {
    background-color: #007bff !important;
    background-image: none !important;
    color: white !important;
    border: none !important;
}

/* Éliminer le gris de tous les éléments de la sidebar */
.card-body .card-header {
    background-color: #007bff !important;
}

/* Anti-gris global pour cette page */
*[class*="bg-light"] {
    background-color: transparent !important;
}

*[class*="text-secondary"] {
    color: #495057 !important;
}

/* Forcer le texte des boutons en blanc */
.btn {
    color: white !important;
}

.btn-primary,
.btn-success,
.btn-warning,
.btn-danger,
.btn-info,
.btn-secondary {
    color: white !important;
}

/* S'assurer que les icônes dans les boutons sont aussi blanches */
.btn i {
    color: white !important;
}
</style>

<!-- Scripts JavaScript -->
@php
    $studentData = null;
    $contextMessage = null;
    
    if(isset($preselectedStudent) && $preselectedStudent) {
        $studentData = json_encode([
            'id' => $preselectedStudent->id,
            'student_id' => $preselectedStudent->student_id,
            'first_name' => $preselectedStudent->first_name,
            'last_name' => $preselectedStudent->last_name,
            'date_of_birth' => $preselectedStudent->date_of_birth->format('d/m/Y'),
            'full_name' => $preselectedStudent->full_name
        ]);
        
        if(isset($fromContext) && $fromContext === 'enrollment') {
            $contextMessage = 'L\'élève nouvellement inscrit a été automatiquement sélectionné pour ce parent.';
        } elseif(isset($fromContext) && $fromContext === 'student') {
            $contextMessage = 'L\'élève nouvellement créé a été automatiquement sélectionné pour ce parent.';
        }
    }
@endphp

@if($studentData)
<div id="preselected-data" 
     data-student='{!! $studentData !!}'
     @if($contextMessage) data-message="{{ $contextMessage }}" @endif
     style="display: none;"></div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSearch = document.getElementById('student_search');
    const studentSuggestions = document.getElementById('student_suggestions');
    const addStudentBtn = document.getElementById('add_student_btn');
    const selectedStudentsList = document.getElementById('selected_students_list');
    const selectedStudentsContainer = document.getElementById('selected_students_container');
    const studentIdsContainer = document.getElementById('student_ids_container');
    const submitBtn = document.getElementById('submit_btn');
    const parentForm = document.getElementById('parentForm');
    
    let selectedStudents = [];
    let searchTimeout;
    
    // Présélectionner l'élève si fourni en paramètre
    const preselectedElement = document.getElementById('preselected-data');
    if (preselectedElement) {
        const studentData = preselectedElement.dataset.student;
        const message = preselectedElement.dataset.message;
        
        if (studentData) {
            setTimeout(function() {
                addStudentToList(JSON.parse(studentData));
                
                if (message) {
                    showSuccess(message);
                }
            }, 100);
        }
    }

    // Recherche d'étudiants avec debounce
    studentSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchStudents(query);
            }, 300);
        } else {
            hideDropdown();
        }
    });

    // Fonction de recherche d'étudiants
    function searchStudents(query) {
        fetch(`/api/students/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSuggestions(data);
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
                hideDropdown();
            });
    }

    // Afficher les suggestions
    function showSuggestions(students) {
        if (students.length === 0) {
            hideDropdown();
            return;
        }

        studentSuggestions.innerHTML = '';
        
        students.forEach(student => {
            // Vérifier si l'étudiant n'est pas déjà sélectionné
            if (!selectedStudents.find(s => s.id === student.id)) {
                const item = document.createElement('div');
                item.className = 'dropdown-item';
                item.innerHTML = `
                    <strong>${student.student_id}</strong> - ${student.first_name} ${student.last_name}
                    <br><small class="text-muted">Né(e) le: ${student.date_of_birth || 'Non spécifié'}</small>
                `;
                item.addEventListener('click', () => {
                    selectStudent(student);
                });
                studentSuggestions.appendChild(item);
            }
        });

        if (studentSuggestions.children.length > 0) {
            studentSuggestions.classList.add('show');
        } else {
            hideDropdown();
        }
    }

    // Masquer le dropdown
    function hideDropdown() {
        studentSuggestions.classList.remove('show');
    }

    // Sélectionner un étudiant
    function selectStudent(student) {
        studentSearch.value = `${student.student_id} - ${student.first_name} ${student.last_name}`;
        hideDropdown();
    }

    // Ajouter un étudiant à la liste
    addStudentBtn.addEventListener('click', function() {
        const query = studentSearch.value.trim();
        if (query === '') {
            showWarning('Veuillez sélectionner un étudiant');
            return;
        }

        // Extraire le matricule de la recherche
        const matricule = query.split(' - ')[0];
        
        // Rechercher l'étudiant par matricule
        fetch(`/api/students/search?q=${encodeURIComponent(matricule)}`)
            .then(response => response.json())
            .then(data => {
                const student = data.find(s => s.student_id === matricule);
                if (student && !selectedStudents.find(s => s.id === student.id)) {
                    addStudentToList(student);
                    studentSearch.value = '';
                    hideDropdown();
                                                } else if (selectedStudents.find(s => s.id === student.id)) {
                                    showWarning('Cet étudiant est déjà ajouté');
                                } else {
                                    showError('Étudiant non trouvé');
                                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showError('Erreur lors de l\'ajout de l\'étudiant');
            });
    });

    // Ajouter un étudiant à la liste sélectionnée
    function addStudentToList(student) {
        selectedStudents.push(student);
        updateSelectedStudentsDisplay();
        updateHiddenInputs();
        updateSubmitButton();
        updateStudentStatus();
    }

    // Supprimer un étudiant de la liste
    function removeStudentFromList(studentId) {
        selectedStudents = selectedStudents.filter(s => s.id !== studentId);
        updateSelectedStudentsDisplay();
        updateHiddenInputs();
        updateSubmitButton();
        updateStudentStatus();
    }

    // Mettre à jour l'affichage des étudiants sélectionnés
    function updateSelectedStudentsDisplay() {
        selectedStudentsList.innerHTML = '';
        
        if (selectedStudents.length === 0) {
            selectedStudentsContainer.classList.remove('has-students');
            return;
        }

        selectedStudentsContainer.classList.add('has-students');

        selectedStudents.forEach(student => {
            const studentCard = document.createElement('div');
            studentCard.className = 'col-md-6 mb-3';
            studentCard.innerHTML = `
                <div class="student-card">
                    <button type="button" class="remove-btn" onclick="removeStudent(${student.id})">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-graduate fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">${student.first_name} ${student.last_name}</h6>
                            <p class="mb-1"><strong>Matricule:</strong> ${student.student_id}</p>
                            <p class="mb-0 text-muted"><small>Né(e) le: ${student.date_of_birth || 'Non spécifié'}</small></p>
                        </div>
                    </div>
                </div>
            `;
            selectedStudentsList.appendChild(studentCard);
        });
    }

    // Mettre à jour les champs cachés
    function updateHiddenInputs() {
        studentIdsContainer.innerHTML = '';
        
        selectedStudents.forEach(student => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = student.id;
            studentIdsContainer.appendChild(input);
        });
    }

    // Mettre à jour le bouton de soumission
    function updateSubmitButton() {
        if (selectedStudents.length === 0) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Ajouter au moins un étudiant';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Enregistrer le Parent';
        }
    }

    // Fonction globale pour supprimer un étudiant (appelée par onclick)
    window.removeStudent = function(studentId) {
        removeStudentFromList(studentId);
    };

    // Masquer les suggestions quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#student_search') && !e.target.closest('#student_suggestions')) {
            hideDropdown();
        }
    });

    // Validation du formulaire
    parentForm.addEventListener('submit', function(e) {
        if (selectedStudents.length === 0) {
            e.preventDefault();
            showError('Veuillez ajouter au moins un étudiant avant de soumettre le formulaire.');
            return;
        }

        // Désactiver le bouton de soumission pour éviter les doubles soumissions
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enregistrement...';
        
        // Le formulaire va maintenant se soumettre normalement et rediriger
    });

    // Initialiser l'état du bouton
    updateSubmitButton();
    
    // Initialiser l'état de l'aide
    updateStudentStatus();
    
    // Ajouter un listener pour le champ téléphone pour la prévisualisation du mot de passe
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            if (this.value.trim()) {
                updatePasswordPreview();
            }
        });
    }

    // Fonction pour mettre à jour la section d'aide
    function updateHelpSection(message, type = 'info') {
        const helpContent = document.getElementById('help-content');
        const helpSection = document.getElementById('help-section');
        
        const iconClass = {
            'error': 'fas fa-exclamation-triangle',
            'warning': 'fas fa-exclamation-circle',
            'success': 'fas fa-check-circle',
            'info': 'fas fa-info-circle'
        }[type] || 'fas fa-info-circle';

        const colorClass = {
            'error': 'text-danger',
            'warning': 'text-warning',
            'success': 'text-success',
            'info': 'text-primary'
        }[type] || 'text-primary';

        const bgClass = {
            'error': 'border-danger',
            'warning': 'border-warning',
            'success': 'border-success',
            'info': 'border-primary'
        }[type] || 'border-primary';

        // Mettre à jour le contenu
        helpContent.innerHTML = `
            <div class="${colorClass}">
                <i class="${iconClass} me-2"></i>
                ${message}
            </div>
        `;

        // Mettre à jour la bordure de la carte
        helpSection.className = `card shadow-sm mt-3 border-start border-3 ${bgClass}`;
        
        // Animation de mise en évidence
        helpSection.style.transform = 'scale(1.02)';
        setTimeout(() => {
            helpSection.style.transform = 'scale(1)';
        }, 200);
    }

    function showError(message) {
        updateHelpSection(message, 'error');
    }

    function showWarning(message) {
        updateHelpSection(message, 'warning');
    }

    function showSuccess(message) {
        updateHelpSection(message, 'success');
    }

    function showInfo(message) {
        updateHelpSection(message, 'info');
    }

    // Fonction pour mettre à jour l'état en fonction des étudiants sélectionnés
    function updateStudentStatus() {
        if (selectedStudents.length === 0) {
            updateHelpSection('⚠️ Vous devez ajouter au moins un étudiant à ce parent', 'warning');
        } else if (selectedStudents.length === 1) {
            updateHelpSection(`✅ ${selectedStudents.length} étudiant sélectionné. Vous pouvez en ajouter d'autres ou continuer.`, 'success');
        } else {
            updateHelpSection(`✅ ${selectedStudents.length} étudiants sélectionnés. Parfait !`, 'success');
        }
    }
    
    function updatePasswordPreview() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput && phoneInput.value.trim()) {
            const generatedPassword = phoneInput.value.trim() + '1234';
            updateHelpSection(`🔑 Mot de passe qui sera généré : <code>${generatedPassword}</code>`, 'info');
        }
    }

    // Rendre les fonctions globales
    window.showError = showError;
    window.showWarning = showWarning;
    window.showSuccess = showSuccess;
    window.showInfo = showInfo;
    window.updateStudentStatus = updateStudentStatus;
});
</script>
@endsection
