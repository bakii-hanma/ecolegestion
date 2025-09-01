@extends('layouts.app')

@section('title', 'Détails de la Classe - ' . ($classe->name ?? 'Classe'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Détails de la Classe</h1>
                <div>
                    <a href="{{ route('bulletins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux bulletins
                    </a>
                    @if($classe->getSafeLevel())
                        <a href="{{ route('bulletins.byLevel', $classe->getSafeLevel()->id) }}" class="btn btn-info">
                            <i class="fas fa-filter"></i> Voir toutes les classes de ce niveau
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i>
                        Informations de la Classe
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Nom:</strong> {{ $classe->name }}</p>
                    
                    @if($classe->getSafeLevel())
                        <p><strong>Niveau:</strong> {{ $classe->getSafeLevel()->name }}</p>
                        <p><strong>Code du niveau:</strong> {{ $classe->getSafeLevel()->code }}</p>
                        <p><strong>Cycle:</strong> 
                            <span class="badge bg-{{ 
                                $classe->getSafeCycle() == 'preprimaire' ? 'primary' : 
                                ($classe->getSafeCycle() == 'primaire' ? 'success' : 
                                ($classe->getSafeCycle() == 'college' ? 'info' : 'warning'))
                            }}">
                                {{ ucfirst($classe->getSafeCycle()) }}
                            </span>
                        </p>
                    @else
                        <p><strong>Niveau:</strong> <span class="text-danger">Non défini</span></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i>
                        Informations sur les étudiants
                    </h5>
                </div>
                <div class="card-body">
                    @if($classe->students && $classe->students->count() > 0)
                        <p><strong>Nombre d'étudiants:</strong> {{ $classe->students->count() }}</p>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                {{ $classe->students->count() }} étudiants
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list"></i> Voir la liste des étudiants
                        </a>
                    @else
                        <p><strong>Nombre d'étudiants:</strong> 0</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucun étudiant n'est inscrit dans cette classe.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($classe->students && $classe->students->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des étudiants ({{ $classe->students->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classe->students as $student)
                                    <tr>
                                        <td>{{ $student->last_name }}</td>
                                        <td>{{ $student->first_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $student->status == 'active' ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('bulletins.student', $student->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir notes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier la classe
                        </a>
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Ajouter des étudiants
                        </a>
                        <a href="#" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                        @if($classe->getSafeLevel())
                                                         <a href="{{ route('bulletins.byCycle', $classe->getSafeCycle()) }}" class="btn btn-warning">
                                 <i class="fas fa-filter"></i> Voir le cycle {{ ucfirst($classe->getSafeCycle()) }}
                             </a>
                        @endif
                    </div>
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
    .progress {
        height: 20px;
    }
</style>
@endsection
