@extends('layouts.app')

@section('title', 'Classes du Niveau - ' . $level->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Classes du Niveau {{ $level->name }}</h1>
                <div>
                    <a href="{{ route('bulletins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux bulletins
                    </a>
                    <a href="{{ route('bulletins.byCycle', $level->cycle) }}" class="btn btn-info">
                        <i class="fas fa-filter"></i> Voir tout le cycle {{ ucfirst($level->cycle) }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i>
                        Informations du Niveau
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom:</strong> {{ $level->name }}</p>
                            <p><strong>Code:</strong> {{ $level->code }}</p>
                            <p><strong>Cycle:</strong> 
                                <span class="badge bg-{{ 
                                    $level->cycle == 'preprimaire' ? 'primary' : 
                                    ($level->cycle == 'primaire' ? 'success' : 
                                    ($level->cycle == 'college' ? 'info' : 'warning'))
                                }}">
                                    {{ ucfirst($level->cycle) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ordre:</strong> {{ $level->order }}</p>
                            <p><strong>Statut:</strong> 
                                <span class="badge bg-{{ $level->is_active ? 'success' : 'secondary' }}">
                                    {{ $level->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                            @if($level->description)
                                <p><strong>Description:</strong> {{ $level->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Classes de ce Niveau</h5>
                </div>
                <div class="card-body">
                    @if($classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom de la Classe</th>
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
                                                <div class="btn-group">
                                                    <a href="{{ route('bulletins.class', $classe->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Voir détails
                                                    </a>
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
                            Aucune classe n'est associée à ce niveau.
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
