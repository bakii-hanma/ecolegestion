@extends('layouts.app')

@section('title', 'Notes de ' . ($student->full_name ?? 'Étudiant'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Notes de {{ $student->full_name }}</h1>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i>
                        Informations de l'étudiant
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Nom complet:</strong> {{ $student->full_name }}</p>
                    <p><strong>Matricule:</strong> {{ $student->student_id }}</p>
                    <p><strong>Date de naissance:</strong> {{ $student->date_of_birth?->format('d/m/Y') ?? 'Non définie' }}</p>
                    <p><strong>Âge:</strong> {{ $student->age ?? 'Non défini' }} ans</p>
                    <p><strong>Statut:</strong> 
                        <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                            {{ $student->status == 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i>
                        Statistiques des notes
                    </h5>
                </div>
                <div class="card-body">
                    @if($student->grades->count() > 0)
                        @php
                            $average = $student->grades->avg('score');
                            $maxScore = $student->grades->max('score');
                            $minScore = $student->grades->min('score');
                            $totalExams = $student->grades->count();
                        @endphp
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Moyenne</h6>
                                        <h4 class="text-primary">{{ number_format($average, 2) }}/20</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Meilleure note</h6>
                                        <h4 class="text-success">{{ number_format($maxScore, 2) }}/20</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Plus basse note</h6>
                                        <h4 class="text-danger">{{ number_format($minScore, 2) }}/20</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Total examens</h6>
                                        <h4 class="text-info">{{ $totalExams }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucune note enregistrée pour cet étudiant.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($student->grades->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book"></i>
                        Détail des notes par matière
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Note</th>
                                    <th>Pourcentage</th>
                                    <th>Type d'examen</th>
                                    <th>Trimestre</th>
                                    <th>Date</th>
                                    <th>Enseignant</th>
                                    <th>Commentaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->grades->sortBy('subject.name') as $grade)
                                    <tr>
                                        <td>
                                            <strong>{{ $grade->subject->name ?? 'Matière inconnue' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $grade->score >= 10 ? 'success' : 'danger' }}">
                                                {{ number_format($grade->score, 2) }}/{{ number_format($grade->max_score, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ number_format($grade->percentage, 1) }}%
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $grade->exam_type_label }}
                                            </span>
                                        </td>
                                        <td>{{ $grade->term }}</td>
                                        <td>{{ $grade->exam_date->format('d/m/Y') }}</td>
                                        <td>
                                            {{ $grade->teacher->full_name ?? 'Enseignant inconnu' }}
                                        </td>
                                        <td>
                                            @if($grade->comments)
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="tooltip" title="{{ $grade->comments }}">
                                                    <i class="fas fa-comment"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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

@section('scripts')
<script>
    // Activer les tooltips Bootstrap
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
