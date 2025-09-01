@extends('layouts.app')

@section('title', 'Élèves de la Classe - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
<li class="breadcrumb-item"><a href="{{ route('classes.show', $class) }}">{{ $class->name }}</a></li>
<li class="breadcrumb-item active">Élèves</li>
@endsection

@push('styles')
<link href="{{ asset('css/classes-enhanced.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #059669, #10b981);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 mb-2 fw-bold">
                                <i class="bi bi-people me-3"></i>
                                Élèves de la Classe {{ $class->name }}
                            </h1>
                            <p class="mb-0 opacity-75">
                                @php
                                    $cycle = $class->getSafeCycle();
                                    $cycleIcons = [
                                        'preprimaire' => '🍼',
                                        'primaire' => '📝',
                                        'college' => '🏫', 
                                        'lycee' => '🎓'
                                    ];
                                @endphp
                                {{ $cycleIcons[$cycle] ?? '📚' }} {{ ucfirst($cycle) }} - {{ $class->getSafeLevelName() }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('enrollments.create') }}?class_id={{ $class->id }}" class="btn btn-light btn-lg">
                                <i class="bi bi-person-plus me-2"></i>
                                Inscrire un élève
                            </a>
                            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline-light">
                                <i class="bi bi-arrow-left me-2"></i>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de la classe -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-1 fw-bold">{{ $students->count() }}</h3>
                            <p class="mb-0 opacity-80 small">Élèves inscrits</p>
                        </div>
                        <div class="ms-3">
                            <div class="bg-light bg-opacity-20 rounded-circle p-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #059669, #10b981);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-1 fw-bold">{{ $class->capacity ?? 0 }}</h3>
                            <p class="mb-0 opacity-80 small">Capacité totale</p>
                        </div>
                        <div class="ms-3">
                            <div class="bg-light bg-opacity-20 rounded-circle p-3">
                                <i class="bi bi-door-open fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            @php
                                $capacity = $class->capacity ?? 0;
                                $occupancy = $capacity > 0 ? round(($students->count() / $capacity) * 100) : 0;
                            @endphp
                            <h3 class="mb-1 fw-bold">{{ $occupancy }}%</h3>
                            <p class="mb-0 opacity-80 small">Taux d'occupation</p>
                        </div>
                        <div class="ms-3">
                            <div class="bg-light bg-opacity-20 rounded-circle p-3">
                                <i class="bi bi-bar-chart fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            @php
                                $remaining = ($class->capacity ?? 0) - $students->count();
                            @endphp
                            <h3 class="mb-1 fw-bold">{{ max(0, $remaining) }}</h3>
                            <p class="mb-0 opacity-80 small">Places restantes</p>
                        </div>
                        <div class="ms-3">
                            <div class="bg-light bg-opacity-20 rounded-circle p-3">
                                <i class="bi bi-plus-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2 text-primary"></i>
                        Recherche et Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>
                                Rechercher un élève
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0" 
                                       placeholder="Nom, prénom ou matricule..." id="searchStudent">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-gender-ambiguous me-1"></i>
                                Sexe
                            </label>
                            <select class="form-select" id="genderFilter">
                                <option value="">Tous</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar me-1"></i>
                                Âge
                            </label>
                            <select class="form-select" id="ageFilter">
                                <option value="">Tous les âges</option>
                                <option value="young">Moins de 10 ans</option>
                                <option value="teen">10-15 ans</option>
                                <option value="older">Plus de 15 ans</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    @if($students->count() > 0)
    <div class="row" id="studentsContainer">
        @foreach($students as $student)
        <div class="col-lg-4 col-md-6 mb-4 student-card" 
             data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}"
             data-matricule="{{ strtolower($student->student_id) }}"
             data-gender="{{ $student->gender }}">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                <!-- Header de l'élève -->
                <div class="card-header border-0 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-person me-2"></i>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </h6>
                        <span class="badge bg-light text-dark">
                            {{ $student->gender == 'M' ? '👨‍🎓' : '👩‍🎓' }}
                        </span>
                    </div>
                </div>

                <!-- Corps de la carte -->
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-card-text text-primary me-2"></i>
                            <strong>Matricule:</strong>
                            <span class="ms-2">{{ $student->student_id }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar text-success me-2"></i>
                            <strong>Âge:</strong>
                            <span class="ms-2">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->age : 'Non renseigné' }} ans</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone text-info me-2"></i>
                            <strong>Contact:</strong>
                            <span class="ms-2">{{ $student->phone ?? 'Non renseigné' }}</span>
                        </div>
                        @if($student->enrollments->first())
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check text-warning me-2"></i>
                            <strong>Inscrit le:</strong>
                            <span class="ms-2">{{ $student->enrollments->first()->created_at->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-footer border-0 bg-light">
                    <div class="d-grid gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Voir
                            </a>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-pencil me-1"></i>Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm unenroll-btn" 
                                    data-student-id="{{ $student->id }}" 
                                    data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                <i class="bi bi-person-dash me-1"></i>Désinscrire
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- État vide -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted mb-3">Aucun élève inscrit</h3>
                    <p class="text-muted mb-4">Cette classe ne contient encore aucun élève. Commencez par inscrire votre premier élève.</p>
                    <a href="{{ route('enrollments.create') }}?class_id={{ $class->id }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>
                        Inscrire le premier élève
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration du filtrage en temps réel
    const searchInput = document.getElementById('searchStudent');
    const genderFilter = document.getElementById('genderFilter');
    const ageFilter = document.getElementById('ageFilter');

    [searchInput, genderFilter, ageFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('input', applyFilters);
            filter.addEventListener('change', applyFilters);
        }
    });

    function applyFilters() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedGender = genderFilter ? genderFilter.value : '';
        const selectedAge = ageFilter ? ageFilter.value : '';
        
        const studentCards = document.querySelectorAll('.student-card');
        let visibleCount = 0;
        
        studentCards.forEach(card => {
            const studentName = card.dataset.name || '';
            const studentMatricule = card.dataset.matricule || '';
            const studentGender = card.dataset.gender || '';
            
            let shouldShow = true;
            
            // Filtre par nom/matricule
            if (searchTerm && !studentName.includes(searchTerm) && !studentMatricule.includes(searchTerm)) {
                shouldShow = false;
            }
            
            // Filtre par sexe
            if (selectedGender && studentGender !== selectedGender) {
                shouldShow = false;
            }
            
            // Afficher/masquer la carte
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Message si aucun résultat
        updateNoResultsMessage(visibleCount);
    }

    function updateNoResultsMessage(visibleCount) {
        let noResultsMessage = document.getElementById('noStudentsMessage');
        
        if (visibleCount === 0 && document.querySelectorAll('.student-card').length > 0) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'noStudentsMessage';
                noResultsMessage.className = 'col-12 text-center py-5';
                noResultsMessage.innerHTML = `
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">Aucun élève trouvé</h4>
                            <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                        </div>
                    </div>
                `;
                document.getElementById('studentsContainer').appendChild(noResultsMessage);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});

// Event listeners pour les boutons de désinscription
document.querySelectorAll('.unenroll-btn').forEach(button => {
    button.addEventListener('click', function() {
        const studentId = this.dataset.studentId;
        const studentName = this.dataset.studentName;
        if (confirm(`Êtes-vous sûr de vouloir désinscrire ${studentName} de cette classe ?`)) {
            console.log('Unenroll student:', studentId);
            // Vous pouvez ajouter ici un appel AJAX pour désinscrire l'élève
        }
    });
});
</script>
@endsection
