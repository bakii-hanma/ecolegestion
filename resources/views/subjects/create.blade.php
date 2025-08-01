@extends('layouts.app')

@section('title', 'Ajouter une Matière - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Matières</a></li>
<li class="breadcrumb-item active">Ajouter une matière</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Ajouter une nouvelle matière
                    </h4>
                </div>
                <div class="card-body">
                    <form id="subjectForm" method="POST" action="{{ route('subjects.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Informations de base -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Informations de la matière</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la matière *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="coefficient" class="form-label">Coefficient *</label>
                                    <input type="number" class="form-control" id="coefficient" name="coefficient" min="0" step="0.5" value="1" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <!-- Cycle et niveau -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Cycle et niveau</h5>
                                
                                <div class="mb-3">
                                    <label for="level_id" class="form-label">Niveau *</label>
                                    <select class="form-select" id="level_id" name="level_id" required>
                                        <option value="">Sélectionner un niveau</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}" data-cycle="{{ $level->cycle }}">
                                                {{ $level->name }} ({{ $level->cycle_label }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Statut</label>
                                    <select class="form-select" id="is_active" name="is_active">
                                        <option value="1" selected>Actif</option>
                                        <option value="0">Inactif</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cycle sélectionné</label>
                                    <div class="form-control-plaintext" id="selectedCycle">
                                        <span class="text-muted">Sélectionnez un niveau pour voir le cycle</span>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Note :</strong> Le coefficient détermine l'importance de la matière dans le calcul des moyennes.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Enregistrer
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level_id');
    const selectedCycleDiv = document.getElementById('selectedCycle');
    
    // Afficher le cycle sélectionné
    levelSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cycle = selectedOption.dataset.cycle;
        
        if (cycle) {
            const cycleLabels = {
                'preprimaire': 'Pré-primaire',
                'primaire': 'Primaire',
                'college': 'Collège',
                'lycee': 'Lycée'
            };
            
            selectedCycleDiv.innerHTML = `<span class="badge bg-info">${cycleLabels[cycle]}</span>`;
        } else {
            selectedCycleDiv.innerHTML = '<span class="text-muted">Sélectionnez un niveau pour voir le cycle</span>';
        }
    });
    
    // Générer automatiquement le code
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    
    nameInput.addEventListener('input', function() {
        if (this.value && !codeInput.value) {
            const code = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 4);
            codeInput.value = code;
        }
    });
    
    // Gérer la soumission du formulaire
    document.getElementById('subjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher un message de succès
                alert(data.message);
                // Rediriger vers la liste des matières
                window.location.href = '{{ route("subjects.index") }}';
            } else {
                // Afficher les erreurs
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'enregistrement.');
        });
    });
});
</script>
@endsection 