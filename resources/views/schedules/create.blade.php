@extends('layouts.app')

@section('title', 'Créer un emploi du temps')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Créer un emploi du temps
                    </h5>
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form action="{{ route('schedules.build') }}" method="GET" id="selectClassForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="class_id" class="form-label">
                                            <i class="fas fa-users me-1"></i>
                                            Sélectionner une classe <span class="text-danger">*</span>
                                        </label>
                                        <select name="class_id" id="class_id" class="form-select" required>
                                            <option value="">-- Choisir une classe --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">
                                                    {{ $class->name }}
                                                    @if($class->getSafeLevelName() !== 'Non défini')
                                                        ({{ $class->getSafeLevelName() }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            Choisissez la classe pour laquelle vous voulez créer l'emploi du temps.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="academic_year_id" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>
                                            Année académique <span class="text-danger">*</span>
                                        </label>
                                        <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}" 
                                                    {{ $currentAcademicYear && $currentAcademicYear->id == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            Sélectionnez l'année académique pour cet emploi du temps.
                                        </div>
                                    </div>
                                </div>

                                <!-- Vérification des emplois du temps existants -->
                                <div id="existingScheduleAlert" class="alert alert-warning d-none">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Attention :</strong> Cette classe a déjà un emploi du temps pour cette année académique. 
                                    En continuant, vous pourrez le modifier ou le remplacer complètement.
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Continuer vers la constitution
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Information -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Comment créer un emploi du temps
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                <h6>1. Sélection de classe</h6>
                                                <p class="small text-muted">
                                                    Choisissez la classe pour laquelle vous voulez créer l'emploi du temps.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                                                <h6>2. Constitution</h6>
                                                <p class="small text-muted">
                                                    Définissez les créneaux horaires et assignez les matières et enseignants.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-save fa-2x text-info mb-2"></i>
                                                <h6>3. Enregistrement</h6>
                                                <p class="small text-muted">
                                                    Sauvegardez l'emploi du temps complet de la classe.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const academicYearSelect = document.getElementById('academic_year_id');
    const existingAlert = document.getElementById('existingScheduleAlert');

    // Vérifier l'existence d'un emploi du temps quand on change la classe ou l'année
    function checkExistingSchedule() {
        const classId = classSelect.value;
        const academicYearId = academicYearSelect.value;

        if (classId && academicYearId) {
            fetch(`/schedules/check-existing?class_id=${classId}&academic_year_id=${academicYearId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        existingAlert.classList.remove('d-none');
                    } else {
                        existingAlert.classList.add('d-none');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification:', error);
                    existingAlert.classList.add('d-none');
                });
        } else {
            existingAlert.classList.add('d-none');
        }
    }

    classSelect.addEventListener('change', checkExistingSchedule);
    academicYearSelect.addEventListener('change', checkExistingSchedule);

    // Vérification initiale si des valeurs sont déjà sélectionnées
    checkExistingSchedule();
});
</script>
@endpush