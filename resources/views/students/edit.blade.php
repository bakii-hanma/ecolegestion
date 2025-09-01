@extends('layouts.app')

@section('title', 'Modifier l\'élève - ' . $student->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('students.index') }}">Élèves</a></li>
<li class="breadcrumb-item active">Modifier {{ $student->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Modifier l'élève</h1>
                    <p class="text-muted">{{ $student->full_name }} - Matricule: {{ $student->student_id }}</p>
                </div>
                <div>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Modification des informations
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editStudentForm" method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informations personnelles de l'élève -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person me-2"></i>
                                    Informations personnelles de l'élève
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Matricule élève</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                               id="student_id" name="student_id" value="{{ old('student_id', $student->student_id) }}" required>
                                    </div>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Sexe <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Masculin</option>
                                            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="place_of_birth" class="form-label">Lieu de naissance</label>
                                    <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                           id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $student->place_of_birth) }}" placeholder="Ville, Pays">
                                    @error('place_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Adresse de l'élève <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" required placeholder="Adresse complète de résidence">{{ old('address', $student->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label">Contact d'urgence</label>
                                    <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                           id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $student->emergency_contact) }}" placeholder="+241 XX XX XX XX">
                                    <div class="form-text">Téléphone à contacter en cas d'urgence</div>
                                    @error('emergency_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="medical_conditions" class="form-label">Conditions médicales particulières</label>
                                    <textarea class="form-control @error('medical_conditions') is-invalid @enderror" 
                                              id="medical_conditions" name="medical_conditions" rows="3" placeholder="Allergies, traitements en cours, conditions particulières...">{{ old('medical_conditions', $student->medical_conditions) }}</textarea>
                                    @error('medical_conditions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Photo et informations scolaires -->
                            <div class="col-md-6">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-image me-2"></i>
                                    Photo et informations scolaires
                                </h6>
                                
                                <!-- Photo actuelle -->
                                <div class="mb-3">
                                    <label class="form-label">Photo actuelle</label>
                                    <div class="text-center mb-3">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" alt="Photo actuelle">
                                        @else
                                            <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                                <span class="text-white fs-1">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Nouvelle photo (optionnel)</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                    <div class="form-text">Formats acceptés: JPG, PNG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="enrollment_date" class="form-label">Date d'inscription <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('enrollment_date') is-invalid @enderror" 
                                               id="enrollment_date" name="enrollment_date" value="{{ old('enrollment_date', $student->enrollment_date->format('Y-m-d')) }}" required>
                                        @error('enrollment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Actif</option>
                                            <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Diplômé</option>
                                            <option value="transferred" {{ old('status', $student->status) === 'transferred' ? 'selected' : '' }}>Transféré</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Informations sur les inscriptions actuelles -->
                                @php
                                    $currentEnrollment = $student->enrollments->where('status', 'active')->first();
                                @endphp
                                
                                @if($currentEnrollment)
                                    <div class="alert alert-info">
                                        <h6><i class="bi bi-info-circle me-2"></i>Inscription actuelle</h6>
                                        <p class="mb-1"><strong>Classe:</strong> {{ $currentEnrollment->schoolClass->name ?? 'Non définie' }}</p>
                                        <p class="mb-1"><strong>Niveau:</strong> {{ $currentEnrollment->schoolClass->level->name ?? 'Non défini' }}</p>
                                        <p class="mb-0"><strong>Année scolaire:</strong> {{ $currentEnrollment->academicYear->name ?? 'Non définie' }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Aucune inscription active</h6>
                                        <p class="mb-0">Cet élève n'a pas d'inscription active pour l'année en cours.</p>
                                    </div>
                                @endif

                                <!-- Informations sur les parents -->
                                @if($student->parents->count() > 0)
                                    <div class="alert alert-success">
                                        <h6><i class="bi bi-people me-2"></i>Parents/Tuteurs associés</h6>
                                        @foreach($student->parents as $parent)
                                            <p class="mb-1"><strong>{{ $parent->first_name }} {{ $parent->last_name }}</strong></p>
                                            <p class="mb-0 small">{{ $parent->phone ?? 'Téléphone non renseigné' }}</p>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Aucun parent associé</h6>
                                        <p class="mb-0">Aucun parent ou tuteur n'est associé à cet élève.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Message d'information -->
                        <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Information :</strong> Cette modification ne concerne que les informations personnelles de l'élève. Pour modifier les inscriptions ou les associations avec les parents, utilisez les sections dédiées.
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la soumission du formulaire
    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Afficher un indicateur de chargement
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Modification en cours...';
        submitBtn.disabled = true;
        
        // Le formulaire se soumettra normalement, la page se rechargera avec les messages de succès/erreur
    });
    
    // Prévisualisation de la nouvelle photo
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Trouver l'image actuelle et la remplacer
                const currentImage = document.querySelector('img.img-fluid.rounded-circle');
                if (currentImage) {
                    currentImage.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
