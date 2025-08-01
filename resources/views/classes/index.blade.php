@extends('layouts.app')

@section('title', 'Gestion des Classes - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item active">Classes</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Classes</h1>
                    <p class="text-muted">Gérez les classes et niveaux de l'établissement</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="bi bi-door-open me-2"></i>
                        Ajouter une classe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $classes->count() ?? 0 }}</h4>
                            <span>Total classes</span>
                        </div>
                        <i class="bi bi-door-open fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $classes->sum('capacity') ?? 0 }}</h4>
                            <span>Capacité totale</span>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $classes->count() > 0 ? round($classes->avg('capacity')) : 0 }}</h4>
                            <span>Capacité moyenne</span>
                        </div>
                        <i class="bi bi-bar-chart fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $levels->count() ?? 0 }}</h4>
                            <span>Niveaux disponibles</span>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom de classe..." id="searchInput">
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
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Tous</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="row">
        @forelse($classes as $class)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $class->name }}</h5>
                        <span class="badge bg-light text-dark">{{ $class->capacity ?? 0 }} places</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary mb-0">{{ $class->capacity ?? 0 }}</div>
                                <small class="text-muted">Capacité</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success mb-0">{{ $class->is_active ? 'Actif' : 'Inactif' }}</div>
                                <small class="text-muted">Statut</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Niveau:</strong> {{ $class->getSafeLevelName() }}
                    </div>
                    <div class="mb-2">
                        <strong>Cycle:</strong> 
                        <span class="badge bg-info">{{ ucfirst($class->getSafeCycle()) }}</span>
                    </div>
                    
                    @if($class->description)
                        <div class="mb-3">
                            <strong>Description:</strong> {{ $class->description }}
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-class-id="{{ $class->id }}" onclick="viewClass(this.dataset.classId)">
                            <i class="bi bi-eye"></i> Voir
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" data-class-id="{{ $class->id }}" onclick="editClass(this.dataset.classId)">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" data-class-id="{{ $class->id }}" onclick="manageStudents(this.dataset.classId)">
                            <i class="bi bi-people"></i> Élèves
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-door-closed fs-1 text-muted mb-3"></i>
                <h4 class="text-muted">Aucune classe trouvée</h4>
                <p class="text-muted">Commencez par créer votre première classe.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="bi bi-plus me-2"></i>
                    Créer une classe
                </button>
            </div>
        </div>
        @endforelse

        <!-- Add New Class Card -->
        @if($classes->count() > 0)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-dashed" style="border: 2px dashed #dee2e6;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <i class="bi bi-plus-circle fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Ajouter une nouvelle classe</h5>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="bi bi-plus me-2"></i>
                        Créer une classe
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($classes->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClassModalLabel">
                    <i class="bi bi-door-open me-2"></i>
                    Ajouter une nouvelle classe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addClassForm" method="POST" action="{{ route('classes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de la classe *</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: 6ème A">
                    </div>
                    
                    <div class="mb-3">
                        <label for="level_id" class="form-label">Niveau *</label>
                        <select class="form-select" id="level_id" name="level_id" required>
                            <option value="">Sélectionner un niveau</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }} ({{ ucfirst($level->cycle) }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="level" class="form-label">Cycle (auto-rempli)</label>
                        <input type="text" class="form-control" id="level" name="level" readonly placeholder="Sera rempli automatiquement">
                    </div>
                    
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacité</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="50" placeholder="30">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description de la classe..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Classe active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Créer la classe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill cycle when level is selected
    document.getElementById('level_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cycleField = document.getElementById('level');
        
        if (this.value) {
            // Extract cycle from the option text (between parentheses)
            const text = selectedOption.text;
            const cycleMatch = text.match(/\(([^)]+)\)/);
            if (cycleMatch) {
                cycleField.value = cycleMatch[1];
            }
        } else {
            cycleField.value = '';
        }
    });
    
    // Form submission
    document.getElementById('addClassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
    });
});

function viewClass(id) {
    // Implement view class functionality
    alert('Voir classe ID: ' + id);
}

function editClass(id) {
    // Implement edit class functionality
    alert('Modifier classe ID: ' + id);
}

function manageStudents(id) {
    // Implement manage students functionality
    alert('Gérer élèves classe ID: ' + id);
}
</script>
@endsection 