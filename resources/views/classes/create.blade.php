@extends('layouts.app')

@section('title', 'Créer une nouvelle classe')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer une nouvelle classe
                    </h4>
                </div>
                <div class="card-body">
                    <form id="classForm" method="POST" action="{{ route('classes.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Informations de base de la classe -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Informations de la classe
                                </h5>
                                
                                <!-- ÉTAPE 1: Sélection du niveau -->
                                <div class="mb-4">
                                    <h6 class="text-secondary mb-2">
                                        <i class="bi bi-1-circle me-2"></i>
                                        Étape 1 : Sélection du niveau
                                    </h6>
                                    <div class="mb-3">
                                        <label for="level_id" class="form-label">Niveau <span class="text-danger">*</span></label>
                                        <select class="form-select @error('level_id') is-invalid @enderror" 
                                                id="level_id" 
                                                name="level_id" 
                                                required>
                                            <option value="">Sélectionner un niveau</option>
                                            @foreach($levels as $level)
                                                <option value="{{ $level->id }}" 
                                                        data-cycle="{{ $level->cycle }}"
                                                        data-level-name="{{ $level->name }}"
                                                        {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                                    {{ $level->name }} ({{ $level->code }}) - {{ ucfirst($level->cycle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('level_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="bi bi-arrow-right me-1"></i>
                                            Commencez par sélectionner le niveau pour débloquer les options suivantes.
                                        </div>
                                    </div>
                                </div>

                                <!-- Champ série pour le lycée -->
                                <div class="mb-3 d-none" id="seriesField">
                                    <label for="series" class="form-label">Série <span class="text-danger">*</span></label>
                                    <select class="form-select @error('series') is-invalid @enderror" 
                                            id="series" 
                                            name="series">
                                        <option value="">Sélectionner une série</option>
                                        <!-- Options dynamiques selon le niveau -->
                                    </select>
                                    @error('series')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        La série détermine les matières spécialisées enseignées dans cette classe.
                                    </div>
                                </div>

                                <!-- ÉTAPE 2: Génération du nom de classe -->
                                <div class="mb-4 d-none" id="nameGenerationSection">
                                    <h6 class="text-secondary mb-2">
                                        <i class="bi bi-2-circle me-2"></i>
                                        Étape 2 : Configuration du nom de classe
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="increment_type" class="form-label">Type d'incrémentation</label>
                                            <select class="form-select" id="increment_type" name="increment_type">
                                                <option value="number">Chiffres (1, 2, 3...)</option>
                                                <option value="letter">Lettres (A, B, C...)</option>
                                            </select>
                                            <div class="form-text" id="incrementTypeHelp">
                                                <i class="bi bi-info-circle me-1"></i>
                                                <span id="incrementTypeStatus">Sélectionnez d'abord un niveau.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="increment_value" class="form-label">Valeur d'incrémentation</label>
                                            <select class="form-select" id="increment_value" name="increment_value" disabled style="background-color: #f8f9fa;">
                                                <option value="">Généré automatiquement</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="bi bi-magic me-1 text-primary"></i>
                                                <span class="text-primary">Valeur générée automatiquement</span> - 1 pour chiffres, A pour lettres
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Choisissez comment différencier les classes du même niveau.
                                    </div>
                                </div>

                                <!-- ÉTAPE 3: Nom généré -->
                                <div class="mb-4">
                                    <h6 class="text-secondary mb-2">
                                        <i class="bi bi-3-circle me-2"></i>
                                        Étape 3 : Nom de la classe
                                    </h6>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               style="background-color: #f8f9fa;"
                                               placeholder="Sélectionnez d'abord un niveau..."
                                               required>
                                        <div class="form-text">
                                            <i class="bi bi-magic me-1 text-primary"></i>
                                            <span class="text-primary">Nom généré automatiquement</span> - Basé sur le niveau, la série (si lycée) et l'incrémentation
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- ÉTAPE 4: Autres informations -->
                                <div class="mb-4">
                                    <h6 class="text-secondary mb-2">
                                        <i class="bi bi-4-circle me-2"></i>
                                        Étape 4 : Informations complémentaires
                                    </h6>
                                    
                                    <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacité maximale</label>
                                    <input type="number" 
                                           class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" 
                                           name="capacity" 
                                           value="{{ old('capacity', 40) }}" 
                                           min="1" 
                                           max="100">
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Classe active
                                        </label>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Section des professeurs -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-person-workspace me-2"></i>
                                    Professeurs de la classe
                                </h5>
                                
                                <div id="teachersContainer">
                                    <!-- Les professeurs seront ajoutés ici dynamiquement -->
                                </div>

                                <div class="mb-3">
                                    <button type="button" 
                                            class="btn btn-success btn-sm" 
                                            id="addTeacherBtn">
                                        <i class="bi bi-plus me-1"></i>
                                        Ajouter un professeur
                                    </button>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Instructions :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Cliquez sur "Ajouter un professeur" pour associer des enseignants à cette classe</li>
                                        <li>Pour le pré-primaire et primaire : sélectionnez un professeur généraliste</li>
                                        <li>Pour le collège et lycée : sélectionnez des professeurs spécialisés (ils sont déjà liés à leurs matières)</li>
                                        <li>Un professeur ne peut être sélectionné qu'une seule fois</li>
                                        <li>Les professeurs qui enseignent la même matière sont automatiquement filtrés</li>
                                        <li>Utilisez le champ de recherche pour filtrer les professeurs par nom</li>
                                        <li>Vous pouvez supprimer un professeur avec le bouton "×"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <span id="submitBtnContent">
                                            <i class="bi bi-check-circle me-2"></i>Créer la classe
                                        </span>
                                        <span id="submitBtnSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Création en cours...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour l'ajout de professeur -->
<template id="teacherTemplate">
    <div class="teacher-entry mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 text-primary">Professeur #<span class="teacher-number"></span></h6>
            <button type="button" class="btn btn-danger btn-sm remove-teacher">
                <i class="bi bi-x"></i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="mb-2">
                    <label class="form-label">Professeur <span class="text-danger">*</span></label>
                    <select class="form-select teacher-select" name="teachers[]" required>
                        <option value="">Sélectionner un professeur</option>
                    </select>
                    <div class="mt-1">
                        <input type="text" class="form-control form-control-sm teacher-search" placeholder="🔍 Rechercher un professeur...">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="teacher-info mt-2 p-2 bg-light rounded" style="display: none;">
            <small class="text-muted">
                <i class="bi bi-person me-1"></i>
                <span class="teacher-name"></span> - 
                <span class="teacher-type"></span>
                <span class="teacher-specialization"></span>
            </small>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script src="{{ asset('js/class-creation.js') }}"></script>
@endpush
