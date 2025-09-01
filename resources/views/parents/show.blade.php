@extends('layouts.app')

@section('title', 'Détails du Parent')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user text-primary me-2"></i>
                        Détails du Parent
                    </h1>
                    <p class="text-muted mb-0">Informations complètes de {{ $parent->first_name }} {{ $parent->last_name }}</p>
                </div>
                <div>
                    <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations du parent -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>
                        Informations Personnelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-user me-1"></i>Nom complet:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $parent->first_name }} {{ $parent->last_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-venus-mars me-1"></i>Genre:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $parent->gender == 'male' ? 'primary' : 'pink' }}">
                                {{ $parent->gender == 'male' ? 'Masculin' : 'Féminin' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-phone me-1"></i>Téléphone principal:</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="tel:{{ $parent->phone }}" class="text-decoration-none">
                                {{ $parent->phone }}
                            </a>
                        </div>
                    </div>

                    @if($parent->phone_2)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-phone me-1"></i>Téléphone secondaire:</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="tel:{{ $parent->phone_2 }}" class="text-decoration-none">
                                {{ $parent->phone_2 }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($parent->email)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-envelope me-1"></i>Email:</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="mailto:{{ $parent->email }}" class="text-decoration-none">
                                {{ $parent->email }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-heart me-1"></i>Relation:</strong>
                        </div>
                        <div class="col-sm-8">
                            @php
                                $relationLabels = [
                                    'father' => 'Père',
                                    'mother' => 'Mère',
                                    'guardian' => 'Tuteur/Tutrice',
                                    'other' => 'Autre'
                                ];
                                $relationColors = [
                                    'father' => 'primary',
                                    'mother' => 'info',
                                    'guardian' => 'warning',
                                    'other' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $relationColors[$parent->relationship] ?? 'secondary' }}">
                                {{ $relationLabels[$parent->relationship] ?? $parent->relationship }}
                            </span>
                        </div>
                    </div>

                    @if($parent->profession)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-briefcase me-1"></i>Profession:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $parent->profession }}
                        </div>
                    </div>
                    @endif

                    @if($parent->workplace)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-building me-1"></i>Lieu de travail:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $parent->workplace }}
                        </div>
                    </div>
                    @endif

                    @if($parent->address)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong><i class="fas fa-map-marker-alt me-1"></i>Adresse:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $parent->address }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statuts et options -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-cog me-2"></i>
                        Statuts et Options
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-star me-1"></i>Contact principal:</strong>
                        </div>
                        <div class="col-sm-6">
                            @if($parent->is_primary_contact)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Oui
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times me-1"></i>Non
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-user-check me-1"></i>Peut récupérer l'enfant:</strong>
                        </div>
                        <div class="col-sm-6">
                            @if($parent->can_pickup)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Oui
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Non
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-calendar-plus me-1"></i>Date d'ajout:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $parent->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-edit me-1"></i>Dernière modification:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $parent->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-users me-1"></i>Nombre d'enfants:</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="badge bg-info fs-6">
                                {{ $parent->students->count() }} enfant{{ $parent->students->count() > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($parent->phone)
                        <a href="tel:{{ $parent->phone }}" class="btn btn-outline-success">
                            <i class="fas fa-phone me-2"></i>Appeler {{ $parent->first_name }}
                        </a>
                        @endif

                        @if($parent->email)
                        <a href="mailto:{{ $parent->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Envoyer un email
                        </a>
                        @endif

                        <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Modifier les informations
                        </a>

                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer ce parent
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des enfants -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Enfants Associés ({{ $parent->students->count() }})
                    </h6>
                    <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i>Gérer les enfants
                    </a>
                </div>
                <div class="card-body">
                    @if($parent->students->count() > 0)
                        <div class="row">
                            @foreach($parent->students as $student)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-primary shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($student->photo)
                                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                                         class="rounded-circle" 
                                                         width="50" 
                                                         height="50"
                                                         alt="Photo de {{ $student->first_name }}">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                                <p class="mb-1 text-sm">
                                                    <strong>Matricule:</strong> {{ $student->student_id }}
                                                </p>
                                                @if($student->date_of_birth)
                                                <p class="mb-1 text-sm text-muted">
                                                    <i class="fas fa-birthday-cake me-1"></i>
                                                    {{ $student->age }} ans
                                                </p>
                                                @endif
                                                <p class="mb-0 text-sm">
                                                    <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $student->status == 'active' ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        @if($student->getCurrentClass())
                                        <div class="mt-3 pt-3 border-top">
                                            <p class="mb-0 text-sm">
                                                <i class="fas fa-school me-1"></i>
                                                <strong>Classe actuelle:</strong> {{ $student->getCurrentClass()->name }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun enfant associé</h5>
                            <p class="text-muted">Ce parent n'a encore aucun enfant associé.</p>
                            <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Associer des enfants
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                    <h5>Êtes-vous sûr de vouloir supprimer ce parent ?</h5>
                </div>
                <div class="alert alert-warning">
                    <strong>Attention :</strong> Cette action supprimera définitivement :
                    <ul class="mt-2 mb-0">
                        <li>Toutes les informations du parent {{ $parent->first_name }} {{ $parent->last_name }}</li>
                        <li>Les liaisons avec ses {{ $parent->students->count() }} enfant(s)</li>
                    </ul>
                </div>
                <p class="text-muted text-center mb-0">
                    <small>Cette action est irréversible.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <form action="{{ route('parents.destroy', $parent->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Oui, supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.text-sm {
    font-size: 0.875rem;
}
</style>

<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
