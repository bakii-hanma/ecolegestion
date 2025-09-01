@extends('layouts.app')

@section('title', 'Gérer les Notes - ' . $student->first_name . ' ' . $student->last_name)

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('grades.index') }}">Notes</a></li>
<li class="breadcrumb-item active">Gérer - {{ $student->first_name }} {{ $student->last_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gérer les Notes</h1>
                    <p class="text-muted">Modifier ou supprimer les notes de {{ $student->first_name }} {{ $student->last_name }}</p>
                </div>
                <div>
                    <a href="{{ route('grades.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                        <i class="bi bi-journal-plus me-2"></i>
                        Ajouter une note
                    </a>
                    <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de l'élève -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Élève:</strong>
                            <p class="mb-0">{{ $student->first_name }} {{ $student->last_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Classe:</strong>
                            <p class="mb-0">{{ $class->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Niveau:</strong>
                            <p class="mb-0">{{ optional($class->level)->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Année scolaire:</strong>
                            <p class="mb-0">{{ $academicYear->name ?? '2024-2025' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des notes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>
                        Notes de l'élève ({{ $grades->count() }} note(s))
                    </h5>
                </div>
                <div class="card-body">
                    @if($grades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Matière</th>
                                        <th>Note</th>
                                        <th>Type</th>
                                        <th>Trimestre</th>
                                        <th>Date</th>
                                        <th>Enseignant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td>
                                                <strong>{{ $grade->subject->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $grade->grade_color }} fs-6">
                                                    {{ $grade->formatted_score }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($grade->exam_type) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $grade->term }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $grade->exam_date ? $grade->exam_date->format('d/m/Y') : 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $grade->teacher->first_name ?? 'N/A' }} {{ $grade->teacher->last_name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('grades.edit', $grade->id) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer"
                                                            data-grade-id="{{ $grade->id }}" 
                                                     data-subject-name="{{ $grade->subject->name ?? 'N/A' }}"
                                                     onclick="deleteGrade(this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Aucune note trouvée</h5>
                            <p class="text-muted">Cet élève n'a pas encore de notes</p>
                            <a href="{{ route('grades.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                                <i class="bi bi-journal-plus me-2"></i>
                                Ajouter la première note
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

@endsection

@push('scripts')
<script>
// Fonction pour afficher les toasts
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Fonction pour supprimer une note
function deleteGrade(button) {
    const gradeId = button.dataset.gradeId;
    const subjectName = button.dataset.subjectName;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer cette note de ${subjectName} ?\n\nCette action est irréversible.`)) {
        // Créer un formulaire temporaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/grades/${gradeId}`;
        
        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Ajouter la méthode DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Ajouter le formulaire au DOM et le soumettre
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
