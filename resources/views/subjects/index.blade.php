@extends('layouts.app')

@section('title', 'Gestion des Matières - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Matières</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Matières</h1>
                    <p class="text-muted">Gérez les matières enseignées par cycle et niveau</p>
                </div>
                <div>
                    <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Ajouter une matière
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subjects->count() ?? 0 }}</h4>
                            <span>Total matières</span>
                        </div>
                        <i class="bi bi-book fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subjects->where('cycle', 'preprimaire')->count() }}</h4>
                            <span>Pré-primaire</span>
                        </div>
                        <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subjects->where('cycle', 'primaire')->count() }}</h4>
                            <span>Primaire</span>
                        </div>
                        <i class="bi bi-pencil fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subjects->where('cycle', 'college')->count() }}</h4>
                            <span>Collège</span>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom de la matière..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cycle</label>
                            <select class="form-select" id="cycleFilter">
                                <option value="">Tous les cycles</option>
                                <option value="preprimaire">Pré-primaire</option>
                                <option value="primaire">Primaire</option>
                                <option value="college">Collège</option>
                                <option value="lycee">Lycée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Niveau</label>
                            <select class="form-select" id="levelFilter">
                                <option value="">Tous les niveaux</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->name }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary fs-6">{{ $subjects->count() ?? 0 }} matières</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="exportBtn">
                                <i class="bi bi-download me-1"></i>Exporter
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="printBtn">
                                <i class="bi bi-printer me-1"></i>Imprimer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="subjectsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Matière</th>
                                    <th>Cycle</th>
                                    <th>Niveau</th>
                                    <th>Coefficient</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $subject->code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-primary rounded-circle">
                                                    {{ strtoupper(substr($subject->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subject->name }}</h6>
                                                <small class="text-muted">{{ $subject->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $subject->level->cycle_label ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $subject->level->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $subject->coefficient }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($subject->description, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($subject->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-id="{{ $subject->id }}" 
                                                    data-name="{{ $subject->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                            Aucune matière trouvée
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($subjects->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $subjects->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la matière <strong id="subjectName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const cycleFilter = document.getElementById('cycleFilter');
    const levelFilter = document.getElementById('levelFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    function filterSubjects() {
        const searchTerm = searchInput.value.toLowerCase();
        const cycle = cycleFilter.value;
        const level = levelFilter.value;
        
        const rows = document.querySelectorAll('#subjectsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const cycleCell = row.querySelector('td:nth-child(3)').textContent;
            const levelCell = row.querySelector('td:nth-child(4)').textContent;
            
            const matchesSearch = text.includes(searchTerm);
            const matchesCycle = !cycle || cycleCell.includes(cycle);
            const matchesLevel = !level || levelCell.includes(level);
            
            row.style.display = (matchesSearch && matchesCycle && matchesLevel) ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterSubjects);
    cycleFilter.addEventListener('change', filterSubjects);
    levelFilter.addEventListener('change', filterSubjects);
    
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        cycleFilter.value = '';
        levelFilter.value = '';
        filterSubjects();
    });
    
    // Delete functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const subjectName = document.getElementById('subjectName');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            subjectName.textContent = name;
            confirmDeleteBtn.onclick = function() {
                fetch(`/subjects/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                });
            };
            
            deleteModal.show();
        });
    });
});
</script>
@endsection 