@extends('layouts.app')

@section('title', 'Aperçu des Paramètres')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-eye text-info me-2"></i>
                        Aperçu des Paramètres
                    </h1>
                    <p class="text-muted">Visualisez comment les paramètres apparaîtront dans l'application</p>
                </div>
                <div>
                    <a href="{{ route('admin.school-settings.index') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de l'établissement -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-school text-primary me-2"></i>
                        Informations de l'Établissement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($settings->school_logo)
                                <img src="{{ $settings->logo_url }}" alt="Logo" class="img-fluid mb-2" style="max-height: 100px;">
                            @else
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <small class="text-muted">Logo</small>
                        </div>
                        <div class="col-md-8">
                            <h4 class="text-primary mb-2">{{ $settings->school_name }}</h4>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $settings->school_address ?? 'Adresse non définie' }}
                            </p>
                            <p class="text-muted mb-1">
                                <i class="fas fa-phone me-1"></i>
                                {{ $settings->school_phone }}
                            </p>
                            @if($settings->school_email)
                                <p class="text-muted mb-1">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ $settings->school_email }}
                                </p>
                            @endif
                            <p class="text-muted mb-0">
                                <i class="fas fa-mailbox me-1"></i>
                                {{ $settings->school_bp }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Direction -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie text-success me-2"></i>
                        Direction
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($settings->school_seal)
                                <img src="{{ $settings->seal_url }}" alt="Sceau" class="img-fluid mb-2" style="max-height: 100px;">
                            @else
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fas fa-stamp fa-3x text-muted"></i>
                                </div>
                            @endif
                            <small class="text-muted">Sceau</small>
                        </div>
                        <div class="col-md-8">
                            <h5 class="text-success mb-2">{{ $settings->principal_title }}</h5>
                            @if($settings->principal_name)
                                <p class="text-muted mb-1">{{ $settings->principal_name }}</p>
                            @else
                                <p class="text-muted mb-1">Nom non défini</p>
                            @endif
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                Année scolaire: {{ $settings->academic_year }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres système -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-warning me-2"></i>
                        Paramètres Système
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Pays:</strong></td>
                                    <td>{{ $settings->country }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ville:</strong></td>
                                    <td>{{ $settings->city }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fuseau horaire:</strong></td>
                                    <td>{{ $settings->timezone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Devise:</strong></td>
                                    <td>{{ $settings->currency }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>{{ $settings->school_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Niveau:</strong></td>
                                    <td>{{ $settings->school_level }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Langue:</strong></td>
                                    <td>{{ $settings->language == 'fr' ? 'Français' : 'English' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut:</strong></td>
                                    <td>
                                        <span class="badge bg-success">Actif</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informations Supplémentaires
                    </h5>
                </div>
                <div class="card-body">
                    @if($settings->school_motto)
                        <div class="mb-3">
                            <h6 class="text-primary">Devise</h6>
                            <p class="text-muted mb-0">{{ $settings->school_motto }}</p>
                        </div>
                    @endif
                    
                    @if($settings->school_description)
                        <div class="mb-3">
                            <h6 class="text-primary">Description</h6>
                            <p class="text-muted mb-0">{{ $settings->school_description }}</p>
                        </div>
                    @endif

                    @if($settings->school_website)
                        <div class="mb-3">
                            <h6 class="text-primary">Site web</h6>
                            <a href="{{ $settings->school_website }}" target="_blank" class="text-decoration-none">
                                {{ $settings->school_website }}
                            </a>
                        </div>
                    @endif

                    @if(!$settings->school_motto && !$settings->school_description && !$settings->school_website)
                        <p class="text-muted text-center">
                            <i class="fas fa-info-circle me-1"></i>
                            Aucune information supplémentaire définie
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Aperçu du bulletin -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt text-danger me-2"></i>
                        Aperçu du Bulletin (Header)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="border p-4 bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                @if($settings->school_logo)
                                    <img src="{{ $settings->logo_url }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
                                @else
                                    <div class="bg-white rounded p-3">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 text-center">
                                <h4 class="mb-1">{{ $settings->school_name }}</h4>
                                <p class="mb-1">{{ $settings->school_address ?? 'Adresse' }}</p>
                                <p class="mb-0">{{ $settings->school_phone }} | {{ $settings->school_bp }}</p>
                            </div>
                            <div class="col-md-3 text-center">
                                @if($settings->school_seal)
                                    <img src="{{ $settings->seal_url }}" alt="Sceau" class="img-fluid" style="max-height: 80px;">
                                @else
                                    <div class="bg-white rounded p-3">
                                        <i class="fas fa-stamp fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <h5 class="text-primary">BULLETIN - 1er TRIMESTRE</h5>
                            <p class="text-muted mb-0">Année Scolaire: {{ $settings->academic_year }}</p>
                        </div>
                    </div>
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

.table td {
    padding: 0.5rem 0;
    border: none;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
