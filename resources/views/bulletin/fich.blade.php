@extends('layouts.app')

@section('title', 'Liste des Classes - Bulletin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Liste des Classes</h1>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation par cycle -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Navigation par Cycle</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('bulletins.byCycle', 'preprimaire') }}" class="btn btn-primary w-100">
                                <i class="fas fa-school"></i> Préprimaire
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('bulletins.byCycle', 'primaire') }}" class="btn btn-success w-100">
                                <i class="fas fa-book"></i> Primaire
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('bulletins.byCycle', 'college') }}" class="btn btn-info w-100">
                                <i class="fas fa-graduation-cap"></i> Collège
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('bulletins.byCycle', 'lycee') }}" class="btn btn-warning w-100">
                                <i class="fas fa-university"></i> Lycée
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affichage par cycle -->
    @foreach(['preprimaire', 'primaire', 'college', 'lycee'] as $cycle)
        @if($levelsByCycle[$cycle]->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-{{ 
                            $cycle == 'preprimaire' ? 'primary' : 
                            ($cycle == 'primaire' ? 'success' : 
                            ($cycle == 'college' ? 'info' : 'warning'))
                        }} text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-{{ 
                                    $cycle == 'preprimaire' ? 'school' : 
                                    ($cycle == 'primaire' ? 'book' : 
                                    ($cycle == 'college' ? 'graduation-cap' : 'university'))
                                }}"></i>
                                Cycle {{ ucfirst($cycle) }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($levelsByCycle[$cycle] as $level)
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-level-up-alt"></i>
                                        Niveau: {{ $level->name }}
                                        <small class="text-muted">({{ $level->code }})</small>
                                    </h6>
                                    
                                    @if($level->classes->count() > 0)
                                        <div class="row">
                                            @foreach($level->classes as $classe)
                                                <div class="col-md-4 col-lg-3 mb-3">
                                                    <div class="card h-100">
                                                        <div class="card-body text-center">
                                                            <h6 class="card-title">{{ $classe->name }}</h6>
                                                            <p class="card-text text-muted small">
                                                                Niveau: {{ $level->name }}
                                                            </p>
                                                <div class="d-flex justify-content-center gap-2">
                                                                                                                <a href="{{ route('bulletins.class', $classe->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                <a href="{{ route('bulletins.byLevel', $level->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-list"></i> Toutes
                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Aucune classe n'est associée à ce niveau.
                                        </div>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Liste complète de toutes les classes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Toutes les Classes</h5>
                </div>
                <div class="card-body">
                    @if($classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom de la Classe</th>
                                        <th>Niveau</th>
                                        <th>Cycle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classes as $classe)
                                        <tr>
                                            <td>
                                                <strong>{{ $classe->name }}</strong>
                                            </td>
                                            <td>
                                                {{ $classe->getSafeLevelName() }}
                                                @if($classe->getSafeLevel())
                                                    <small class="text-muted">({{ $classe->getSafeLevel()->code }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $cycle = $classe->getSafeCycle();
                                                @endphp
                                                <span class="badge bg-{{ 
                                                    $cycle == 'preprimaire' ? 'primary' : 
                                                    ($cycle == 'primaire' ? 'success' : 
                                                    ($cycle == 'college' ? 'info' : 'warning'))
                                                }}">
                                                    {{ ucfirst($cycle) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                                                                         <a href="{{ route('bulletins.class', $classe->id) }}" 
                                                        class="btn btn-sm btn-primary">
                                                         <i class="fas fa-eye"></i>
                                                     </a>
                                                     @if($classe->getSafeLevel())
                                                         <a href="{{ route('bulletins.byLevel', $classe->getSafeLevel()->id) }}" 
                                                            class="btn btn-sm btn-info">
                                                             <i class="fas fa-filter"></i>
                                                         </a>
                                                     @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Aucune classe n'a été créée dans le système.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .badge {
        font-size: 0.8em;
    }
</style>
@endsection
