@extends('layouts.app')

@section('title', 'Classes par Cycle - ' . $cycleName)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Classes du Cycle {{ $cycleName }}</h1>
                <div>
                    <a href="{{ route('bulletins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux bulletins
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($levels->count() > 0)
        @foreach($levels as $level)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-level-up-alt"></i>
                                Niveau: {{ $level->name }}
                                <small class="text-muted">({{ $level->code }})</small>
                            </h5>
                        </div>
                        <div class="card-body">
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
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Aucun niveau trouvé pour le cycle {{ $cycleName }}.
        </div>
    @endif
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
</style>
@endsection
