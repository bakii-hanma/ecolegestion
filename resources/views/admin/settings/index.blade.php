@extends('layouts.app')

@section('title', 'Paramètres de l\'Établissement')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Paramètres de l'Établissement
                    </h1>
                    <p class="text-muted">Configurez les informations de votre établissement scolaire</p>
                </div>
                <div>
                    <a href="{{ route('admin.school-settings.preview') }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> Aperçu
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Modifier les Paramètres
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.school-settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Informations Générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Informations Générales
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_name" class="form-label">Nom de l'établissement *</label>
                                <input type="text" class="form-control @error('school_name') is-invalid @enderror" 
                                       id="school_name" name="school_name" 
                                       value="{{ old('school_name', $settings->school_name) }}" required>
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_type" class="form-label">Type d'établissement *</label>
                                <select class="form-select @error('school_type') is-invalid @enderror" 
                                        id="school_type" name="school_type" required>
                                    <option value="École Maternelle" {{ old('school_type', $settings->school_type) == 'École Maternelle' ? 'selected' : '' }}>École Maternelle</option>
                                    <option value="École Primaire" {{ old('school_type', $settings->school_type) == 'École Primaire' ? 'selected' : '' }}>École Primaire</option>
                                    <option value="Collège" {{ old('school_type', $settings->school_type) == 'Collège' ? 'selected' : '' }}>Collège</option>
                                    <option value="Lycée" {{ old('school_type', $settings->school_type) == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                                    <option value="Université" {{ old('school_type', $settings->school_type) == 'Université' ? 'selected' : '' }}>Université</option>
                                </select>
                                @error('school_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_level" class="form-label">Niveau *</label>
                                <select class="form-select @error('school_level') is-invalid @enderror" 
                                        id="school_level" name="school_level" required>
                                    <option value="Maternelle" {{ old('school_level', $settings->school_level) == 'Maternelle' ? 'selected' : '' }}>Maternelle</option>
                                    <option value="Primaire" {{ old('school_level', $settings->school_level) == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                                    <option value="Secondaire" {{ old('school_level', $settings->school_level) == 'Secondaire' ? 'selected' : '' }}>Secondaire</option>
                                    <option value="Supérieur" {{ old('school_level', $settings->school_level) == 'Supérieur' ? 'selected' : '' }}>Supérieur</option>
                                </select>
                                @error('school_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="academic_year" class="form-label">Année scolaire *</label>
                                <input type="text" class="form-control @error('academic_year') is-invalid @enderror" 
                                       id="academic_year" name="academic_year" 
                                       value="{{ old('academic_year', $settings->academic_year) }}" required>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact et Localisation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Contact et Localisation
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_address" class="form-label">Adresse</label>
                                <textarea class="form-control @error('school_address') is-invalid @enderror" 
                                          id="school_address" name="school_address" rows="2">{{ old('school_address', $settings->school_address) }}</textarea>
                                @error('school_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_phone" class="form-label">Téléphone *</label>
                                <input type="text" class="form-control @error('school_phone') is-invalid @enderror" 
                                       id="school_phone" name="school_phone" 
                                       value="{{ old('school_phone', $settings->school_phone) }}" required>
                                @error('school_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('school_email') is-invalid @enderror" 
                                       id="school_email" name="school_email" 
                                       value="{{ old('school_email', $settings->school_email) }}">
                                @error('school_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_website" class="form-label">Site web</label>
                                <input type="url" class="form-control @error('school_website') is-invalid @enderror" 
                                       id="school_website" name="school_website" 
                                       value="{{ old('school_website', $settings->school_website) }}">
                                @error('school_website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_bp" class="form-label">Boîte postale *</label>
                                <input type="text" class="form-control @error('school_bp') is-invalid @enderror" 
                                       id="school_bp" name="school_bp" 
                                       value="{{ old('school_bp', $settings->school_bp) }}" required>
                                @error('school_bp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Ville *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" 
                                       value="{{ old('city', $settings->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Images et Logos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-images me-1"></i>
                                    Images et Logos
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_logo" class="form-label">Logo de l'établissement</label>
                                <input type="file" class="form-control @error('school_logo') is-invalid @enderror" 
                                       id="school_logo" name="school_logo" accept="image/*">
                                <div class="form-text">Format: JPG, PNG, GIF. Taille max: 2MB</div>
                                @error('school_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($settings->school_logo)
                                    <div class="mt-2">
                                        <img src="{{ $settings->logo_url }}" alt="Logo actuel" class="img-thumbnail" style="max-height: 100px;">
                                        <small class="text-muted d-block">Logo actuel</small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_seal" class="form-label">Sceau de l'établissement</label>
                                <input type="file" class="form-control @error('school_seal') is-invalid @enderror" 
                                       id="school_seal" name="school_seal" accept="image/*">
                                <div class="form-text">Format: JPG, PNG, GIF. Taille max: 2MB</div>
                                @error('school_seal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($settings->school_seal)
                                    <div class="mt-2">
                                        <img src="{{ $settings->seal_url }}" alt="Sceau actuel" class="img-thumbnail" style="max-height: 100px;">
                                        <small class="text-muted d-block">Sceau actuel</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Direction -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Direction
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="principal_name" class="form-label">Nom du directeur</label>
                                <input type="text" class="form-control @error('principal_name') is-invalid @enderror" 
                                       id="principal_name" name="principal_name" 
                                       value="{{ old('principal_name', $settings->principal_name) }}">
                                @error('principal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="principal_title" class="form-label">Titre du directeur *</label>
                                <input type="text" class="form-control @error('principal_title') is-invalid @enderror" 
                                       id="principal_title" name="principal_title" 
                                       value="{{ old('principal_title', $settings->principal_title) }}" required>
                                @error('principal_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Paramètres Système -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-1"></i>
                                    Paramètres Système
                                </h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="country" class="form-label">Pays *</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" 
                                       value="{{ old('country', $settings->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="timezone" class="form-label">Fuseau horaire *</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" 
                                        id="timezone" name="timezone" required>
                                    <option value="Africa/Libreville" {{ old('timezone', $settings->timezone) == 'Africa/Libreville' ? 'selected' : '' }}>Africa/Libreville</option>
                                    <option value="Africa/Lagos" {{ old('timezone', $settings->timezone) == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
                                    <option value="Europe/Paris" {{ old('timezone', $settings->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="currency" class="form-label">Devise *</label>
                                <select class="form-select @error('currency') is-invalid @enderror" 
                                        id="currency" name="currency" required>
                                    <option value="FCFA" {{ old('currency', $settings->currency) == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                                    <option value="EUR" {{ old('currency', $settings->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="USD" {{ old('currency', $settings->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="language" class="form-label">Langue *</label>
                                <select class="form-select @error('language') is-invalid @enderror" 
                                        id="language" name="language" required>
                                    <option value="fr" {{ old('language', $settings->language) == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ old('language', $settings->language) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations Supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Informations Supplémentaires
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_motto" class="form-label">Devise de l'établissement</label>
                                <textarea class="form-control @error('school_motto') is-invalid @enderror" 
                                          id="school_motto" name="school_motto" rows="2">{{ old('school_motto', $settings->school_motto) }}</textarea>
                                @error('school_motto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_description" class="form-label">Description</label>
                                <textarea class="form-control @error('school_description') is-invalid @enderror" 
                                          id="school_description" name="school_description" rows="2">{{ old('school_description', $settings->school_description) }}</textarea>
                                @error('school_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Enregistrer les Paramètres
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

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.text-primary {
    color: #007bff !important;
}
</style>
@endsection
