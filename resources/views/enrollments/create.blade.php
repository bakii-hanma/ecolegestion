@extends('layouts.app')

@section('title', 'Nouvelle Inscription - StudiaGabon')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Inscriptions</a></li>
<li class="breadcrumb-item active">Nouvelle inscription</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête avec étapes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Nouvelle Inscription
                            </h2>
                            <p class="mb-0 opacity-75">Processus d'inscription en 2 étapes : inscription puis création du profil élève</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-primary fs-6 px-3 py-2">
                                <i class="bi bi-calendar-event me-1"></i>
                                Année {{ now()->year }}-{{ now()->year + 1 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire principal -->
    <form id="enrollmentForm" method="POST" action="{{ route('enrollments.store') }}">
        @csrf
        
        <div class="row">
            <!-- Colonne 1 : Informations de l'inscrit -->
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            Informations de l'inscrit
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="applicant_first_name" class="form-label">
                                <i class="bi bi-person me-1"></i>
                                Prénom <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="applicant_first_name" name="applicant_first_name" required placeholder="Prénom de l'élève">
                        </div>
                        
                        <div class="mb-3">
                            <label for="applicant_last_name" class="form-label">
                                <i class="bi bi-person me-1"></i>
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="applicant_last_name" name="applicant_last_name" required placeholder="Nom de famille">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="applicant_date_of_birth" class="form-label">
                                        <i class="bi bi-calendar me-1"></i>
                                        Date de naissance <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="applicant_date_of_birth" name="applicant_date_of_birth" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="applicant_gender" class="form-label">
                                        <i class="bi bi-gender-ambiguous me-1"></i>
                                        Sexe <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="applicant_gender" name="applicant_gender" required>
                                        <option value="">Choisir...</option>
                                        <option value="male">🧑 Masculin</option>
                                        <option value="female">👩 Féminin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="applicant_phone" class="form-label">
                                <i class="bi bi-telephone me-1"></i>
                                Téléphone de l'inscrit
                            </label>
                            <input type="text" class="form-control" id="applicant_phone" name="applicant_phone" placeholder="+241 XX XX XX XX">
                        </div>
                        
                        <div class="mb-3">
                            <label for="applicant_email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>
                                Email de l'inscrit
                            </label>
                            <input type="email" class="form-control" id="applicant_email" name="applicant_email" placeholder="email@exemple.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="applicant_address" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>
                                Adresse <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="applicant_address" name="applicant_address" rows="3" required placeholder="Adresse complète de résidence"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne 2 : Informations scolaires -->
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-mortarboard me-2"></i>
                            Informations scolaires
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="academic_year_id" class="form-label">
                                <i class="bi bi-calendar-range me-1"></i>
                                Année scolaire <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Sélectionner l'année...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                        🗓️ {{ $year->name }} {{ $year->is_current ? '(Actuelle)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cycle" class="form-label">
                                <i class="bi bi-diagram-3 me-1"></i>
                                Cycle d'enseignement <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="cycle" name="cycle" required>
                                <option value="">Choisir le cycle...</option>
                                <option value="preprimaire">🏫 Pré-primaire (PS, MS, GS)</option>
                                <option value="primaire">📚 Primaire (CP, CE1, CE2, CM1, CM2)</option>
                                <option value="college">🎓 Collège (6ème, 5ème, 4ème, 3ème)</option>
                                <option value="lycee">🏛️ Lycée (2nde, 1ère, Terminal)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="level_id" class="form-label">
                                <i class="bi bi-bookmark me-1"></i>
                                Niveau <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="level_id" name="level_id" required disabled>
                                <option value="">Choisir d'abord le cycle...</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="class_id" class="form-label">
                                <i class="bi bi-door-open me-1"></i>
                                Classe <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="class_id" name="class_id" required disabled>
                                <option value="">Choisir d'abord le niveau...</option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="class-capacity-info"></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="enrollment_date" class="form-label">
                                <i class="bi bi-calendar-plus me-1"></i>
                                Date d'inscription <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_new_enrollment" name="is_new_enrollment" checked>
                                <label class="form-check-label" for="is_new_enrollment">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Nouvel élève (première inscription)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne 3 : Informations de paiement -->
            <div class="col-lg-4">
                <!-- Informations de paiement -->
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>
                            Paiement des frais
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="total_fees" class="form-label">
                                <i class="bi bi-cash-coin me-1"></i>
                                Frais totaux <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg" id="total_fees" name="total_fees" step="0.01" min="0" required placeholder="0.00">
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount_paid" class="form-label">
                                <i class="bi bi-wallet2 me-1"></i>
                                Montant payé <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg" id="amount_paid" name="amount_paid" step="0.01" min="0" required placeholder="0.00">
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-calculator me-1"></i>
                                Reste à payer
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control bg-light" id="balance_due" name="balance_due" readonly>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="form-text">
                                <span id="payment-status" class="badge bg-secondary">Calculé automatiquement</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">
                                <i class="bi bi-credit-card-2-front me-1"></i>
                                Mode de paiement
                            </label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="">Choisir...</option>
                                <option value="cash">💵 Espèces</option>
                                <option value="bank_transfer">🏦 Virement bancaire</option>
                                <option value="mobile_money">📱 Mobile Money</option>
                                <option value="check">📄 Chèque</option>
                                <option value="card">💳 Carte bancaire</option>
                            </select>
                        </div>

                        <!-- Section Mobile Money (affichée seulement si Mobile Money est sélectionné) -->
                        <div id="mobile_money_section" class="mb-3" style="display: none;">
                            <label class="form-label">
                                <i class="bi bi-phone me-1"></i>
                                Choisir votre opérateur Mobile Money
                            </label>
                            
                            <!-- Sélection d'opérateur avec images -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mobile_money_provider" id="airtel_money" value="airtel">
                                        <label class="form-check-label d-flex align-items-center p-3 border rounded" for="airtel_money" style="cursor: pointer;">
                                            <img src="{{ asset('images/payment-methods/airtel-money.png') }}" alt="Airtel Money" style="width: 40px; height: 40px; margin-right: 10px;">
                                            <div>
                                                <strong>Airtel Money</strong>
                                                <br><small class="text-muted">Numéros 07XXXXXXX - Paiement sécurisé</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mobile_money_provider" id="moov_money" value="moov">
                                        <label class="form-check-label d-flex align-items-center p-3 border rounded" for="moov_money" style="cursor: pointer;">
                                            <img src="{{ asset('images/payment-methods/moov-money.jpg') }}" alt="Moov Money" style="width: 40px; height: 40px; margin-right: 10px;">
                                            <div>
                                                <strong>Moov Money</strong>
                                                <br><small class="text-muted">Numéros 06XXXXXXX - Paiement sécurisé</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Champ numéro de téléphone mobile money -->
                            <div class="mt-3" id="mobile_number_field" style="display: none;">
                                <label for="mobile_money_number" class="form-label">
                                    <i class="bi bi-phone-fill me-1"></i>
                                    Numéro de téléphone Mobile Money
                                </label>
                                <input type="tel" class="form-control" id="mobile_money_number" name="mobile_money_number" 
                                       placeholder="Ex: 07XXXXXXX" pattern="0[67][0-9]{7}" maxlength="9">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="mobile_money_hint">Saisissez votre numéro Mobile Money (9 chiffres)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_due_date" class="form-label">
                                <i class="bi bi-calendar-x me-1"></i>
                                Date limite (si reste à payer)
                            </label>
                            <input type="date" class="form-control" id="payment_due_date" name="payment_due_date">
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_notes" class="form-label">
                                <i class="bi bi-chat-left-text me-1"></i>
                                Notes sur le paiement
                            </label>
                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="2" placeholder="Remarques particulières..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Retour aux inscriptions
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-warning btn-lg me-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i>
                                    Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Enregistrer l'inscription
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="enrollmentSuccessModal" tabindex="-1" aria-labelledby="enrollmentSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="enrollmentSuccessModalLabel">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Inscription enregistrée avec succès !
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="enrollmentDetails">
                    <!-- Les détails seront chargés ici -->
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Prochaine étape :</strong> Voulez-vous créer le profil complet de l'élève maintenant ou le faire plus tard ?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-clock me-2"></i>
                    Plus tard
                </button>
                <button type="button" class="btn btn-warning" id="viewReceiptBtn">
                    <i class="bi bi-printer me-2"></i>
                    Imprimer le reçu
                </button>
                <button type="button" class="btn btn-primary" id="createStudentBtn">
                    <i class="bi bi-person-plus me-2"></i>
                    Créer le profil élève
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour proposer l'enregistrement des parents -->
<div class="modal fade" id="parentRegistrationModal" tabindex="-1" aria-labelledby="parentRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="parentRegistrationModalLabel">
                    <i class="bi bi-people-fill me-2"></i>
                    Enregistrer les parents de l'élève
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle me-3 fs-4"></i>
                    <div>
                        <strong>Inscription réussie !</strong><br>
                        L'élève <strong id="studentNameInModal"></strong> a été inscrit avec succès. 
                        Souhaitez-vous maintenant enregistrer les informations de ses parents/tuteurs ?
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-clock-history fs-2 text-warning mb-3"></i>
                                <h6>Plus tard</h6>
                                <p class="text-muted small">Vous pourrez ajouter les parents plus tard via la section "Parents" du système.</p>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-clock me-2"></i>
                                    Reporter à plus tard
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="bi bi-person-plus-fill fs-2 text-success mb-3"></i>
                                <h6>Maintenant</h6>
                                <p class="text-muted small">Enregistrer immédiatement les informations des parents/tuteurs de cet élève.</p>
                                <button type="button" class="btn btn-success" id="addParentNowBtn">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Ajouter maintenant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-lightbulb me-2"></i>
                            Pourquoi enregistrer les parents ?
                        </h6>
                        <ul class="text-muted small">
                            <li>Accès au portail parent pour consulter les notes et absences</li>
                            <li>Réception de notifications automatiques</li>
                            <li>Gestion des paiements en ligne</li>
                            <li>Communication directe avec l'établissement</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Vous pourrez toujours ajouter ou modifier les parents plus tard.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cycleSelect = document.getElementById('cycle');
    const levelSelect = document.getElementById('level_id');
    const classSelect = document.getElementById('class_id');
    const totalFeesInput = document.getElementById('total_fees');
    const amountPaidInput = document.getElementById('amount_paid');
    const balanceDueInput = document.getElementById('balance_due');
    const paymentStatusSpan = document.getElementById('payment-status');
    const classCapacityInfo = document.getElementById('class-capacity-info');

    // Chargement des niveaux par cycle
    cycleSelect.addEventListener('change', function() {
        const cycle = this.value;
        levelSelect.innerHTML = '<option value="">Chargement...</option>';
        levelSelect.disabled = true;
        classSelect.innerHTML = '<option value="">Choisir d\'abord le niveau...</option>';
        classSelect.disabled = true;
        
        if (cycle) {
            fetch(`/api/levels-by-cycle?cycle=${cycle}`)
                .then(response => response.json())
                .then(levels => {
                    levelSelect.innerHTML = '<option value="">Choisir le niveau...</option>';
                    levels.forEach(level => {
                        levelSelect.innerHTML += `<option value="${level.id}">${level.name}</option>`;
                    });
                    levelSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    levelSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        } else {
            levelSelect.innerHTML = '<option value="">Choisir d\'abord le cycle...</option>';
            levelSelect.disabled = true;
        }
    });

    // Chargement des classes par niveau
    levelSelect.addEventListener('change', function() {
        const levelId = this.value;
        classSelect.innerHTML = '<option value="">Chargement...</option>';
        classSelect.disabled = true;
        
        if (levelId) {
            fetch(`/api/students/classes-by-level?level_id=${levelId}`)
                .then(response => response.json())
                .then(classes => {
                    classSelect.innerHTML = '<option value="">Choisir la classe...</option>';
                    classes.forEach(classItem => {
                        classSelect.innerHTML += `<option value="${classItem.id}">${classItem.name} (${classItem.capacity} places)</option>`;
                    });
                    classSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    classSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        } else {
            classSelect.innerHTML = '<option value="">Choisir d\'abord le niveau...</option>';
            classSelect.disabled = true;
        }
    });

    // Affichage des infos de classe
    classSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value && selectedOption.text.includes('(')) {
            const capacity = selectedOption.text.match(/\((\d+) places\)/);
            if (capacity) {
                classCapacityInfo.textContent = `Capacité de la classe : ${capacity[1]} places`;
                classCapacityInfo.className = 'text-info';
            }
        } else {
            classCapacityInfo.textContent = '';
        }
    });

    // Calcul automatique du reste à payer
    function calculateBalance() {
        const total = parseFloat(totalFeesInput.value) || 0;
        const paid = parseFloat(amountPaidInput.value) || 0;
        const balance = total - paid;
        
        balanceDueInput.value = balance.toFixed(2);
        
        if (balance === 0) {
            paymentStatusSpan.textContent = 'Payé intégralement';
            paymentStatusSpan.className = 'badge bg-success';
        } else if (balance > 0 && paid > 0) {
            paymentStatusSpan.textContent = 'Paiement partiel';
            paymentStatusSpan.className = 'badge bg-warning';
        } else if (balance > 0 && paid === 0) {
            paymentStatusSpan.textContent = 'Non payé';
            paymentStatusSpan.className = 'badge bg-danger';
        } else {
            paymentStatusSpan.textContent = 'Trop-perçu';
            paymentStatusSpan.className = 'badge bg-info';
        }
    }

    totalFeesInput.addEventListener('input', calculateBalance);
    amountPaidInput.addEventListener('input', calculateBalance);

    // Soumission du formulaire
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enregistrement...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les détails dans le modal
                document.getElementById('enrollmentDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-person me-1"></i> Inscrit :</h6>
                            <p><strong>${data.enrollment.applicant_first_name} ${data.enrollment.applicant_last_name}</strong></p>
                            <p>Classe : <span class="badge bg-primary">${data.enrollment.school_class.name}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-receipt me-1"></i> Paiement :</h6>
                            <p>Total : <strong>${data.enrollment.total_fees} FCFA</strong></p>
                            <p>Payé : <strong>${data.enrollment.amount_paid} FCFA</strong></p>
                            <p>Reste : <strong>${data.enrollment.balance_due} FCFA</strong></p>
                            <p>Reçu N° : <span class="badge bg-success">${data.receipt_number}</span></p>
                        </div>
                    </div>
                `;
                
                // Configurer les boutons du modal
                document.getElementById('createStudentBtn').onclick = function() {
                    window.location.href = `/enrollments/${data.enrollment.id}/create-student`;
                };
                
                document.getElementById('viewReceiptBtn').onclick = function() {
                    // Ouvrir le PDF et déclencher l'impression
                    const printWindow = window.open(`/enrollments/${data.enrollment.id}/receipt/download`, '_blank');
                    
                    if (printWindow) {
                        printWindow.onload = function() {
                            setTimeout(function() {
                                printWindow.print();
                            }, 1000);
                        };
                    }
                };
                
                // Afficher le modal
                const successModal = new bootstrap.Modal(document.getElementById('enrollmentSuccessModal'));
                successModal.show();
                
                // Après fermeture du modal de succès, proposer l'ajout des parents
                document.getElementById('enrollmentSuccessModal').addEventListener('hidden.bs.modal', function () {
                    // Attendre un peu avant d'afficher le modal parent
                    setTimeout(function() {
                        showParentRegistrationModal(data.enrollment);
                    }, 500);
                });
                
                // Réinitialiser le formulaire
                document.getElementById('enrollmentForm').reset();
                levelSelect.innerHTML = '<option value="">Choisir d\'abord le cycle...</option>';
                levelSelect.disabled = true;
                classSelect.innerHTML = '<option value="">Choisir d\'abord le niveau...</option>';
                classSelect.disabled = true;
                balanceDueInput.value = '';
                paymentStatusSpan.textContent = 'Calculé automatiquement';
                paymentStatusSpan.className = 'badge bg-secondary';
                classCapacityInfo.textContent = '';
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'enregistrement.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Gestion de la section Mobile Money
    const paymentMethodSelect = document.getElementById('payment_method');
    const mobileMoneySectionDiv = document.getElementById('mobile_money_section');
    const mobileNumberFieldDiv = document.getElementById('mobile_number_field');
    const mobileProviderRadios = document.querySelectorAll('input[name="mobile_money_provider"]');

    // Afficher/masquer la section mobile money selon la méthode de paiement
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'mobile_money') {
            mobileMoneySectionDiv.style.display = 'block';
        } else {
            mobileMoneySectionDiv.style.display = 'none';
            mobileNumberFieldDiv.style.display = 'none';
            // Réinitialiser les champs mobile money
            mobileProviderRadios.forEach(radio => radio.checked = false);
            document.getElementById('mobile_money_number').value = '';
        }
    });

    // Afficher le champ numéro quand un opérateur est sélectionné
    mobileProviderRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                mobileNumberFieldDiv.style.display = 'block';
                
                // Mettre à jour le placeholder et le message selon l'opérateur gabonais
                const mobileNumberInput = document.getElementById('mobile_money_number');
                const mobileMoneyHint = document.getElementById('mobile_money_hint');
                
                if (this.value === 'airtel') {
                    mobileNumberInput.placeholder = 'Ex: 076527007 (Airtel)';
                    mobileMoneyHint.innerHTML = '<strong>Airtel :</strong> Numéros commençant par 07 (9 chiffres)';
                } else if (this.value === 'moov') {
                    mobileNumberInput.placeholder = 'Ex: 066527007 (Moov/Libertis)';
                    mobileMoneyHint.innerHTML = '<strong>Moov/Libertis :</strong> Numéros commençant par 06 (9 chiffres)';
                }
            }
        });
    });

    // Validation du formulaire pour mobile money
    const originalFormSubmit = document.getElementById('enrollmentForm').onsubmit;
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        if (paymentMethodSelect.value === 'mobile_money') {
            const selectedProvider = document.querySelector('input[name="mobile_money_provider"]:checked');
            const mobileNumber = document.getElementById('mobile_money_number').value;
            
            if (!selectedProvider) {
                e.preventDefault();
                alert('Veuillez sélectionner un opérateur Mobile Money.');
                return false;
            }
            
            if (!mobileNumber.trim()) {
                e.preventDefault();
                alert('Veuillez entrer votre numéro de téléphone Mobile Money.');
                return false;
            }
            
            // Validation spécifique selon l'opérateur gabonais
            const cleanNumber = mobileNumber.replace(/\s/g, ''); // Supprimer les espaces
            
            if (selectedProvider.value === 'airtel') {
                if (!/^07[0-9]{7}$/.test(cleanNumber)) {
                    e.preventDefault();
                    alert('Numéro Airtel invalide. Utilisez le format : 07XXXXXXX (ex: 076527007)');
                    return false;
                }
            } else if (selectedProvider.value === 'moov') {
                if (!/^06[0-9]{7}$/.test(cleanNumber)) {
                    e.preventDefault();
                    alert('Numéro Moov/Libertis invalide. Utilisez le format : 06XXXXXXX (ex: 066527007)');
                    return false;
                }
            }
        }
    });
});

// Fonction pour afficher le modal d'enregistrement des parents
function showParentRegistrationModal(enrollment) {
    // Mettre à jour le nom de l'élève dans le modal
    document.getElementById('studentNameInModal').textContent = `${enrollment.applicant_first_name} ${enrollment.applicant_last_name}`;
    
    // Configurer le bouton "Ajouter maintenant"
    document.getElementById('addParentNowBtn').onclick = function() {
        // Rediriger vers la page de création de parent avec l'ID de l'élève en paramètre
        window.location.href = `/parents/create?student_id=${enrollment.id}&from=enrollment`;
    };
    
    // Afficher le modal
    const parentModal = new bootstrap.Modal(document.getElementById('parentRegistrationModal'));
    parentModal.show();
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.form-control-lg, .form-select-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.input-group-text {
    font-weight: 600;
}

.badge {
    font-size: 0.9rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

/* Styles pour Mobile Money */
.form-check-label {
    transition: all 0.3s ease;
}

.form-check-input:checked + .form-check-label {
    background-color: #e3f2fd !important;
    border-color: #007bff !important;
    border-width: 2px !important;
}

.form-check-label:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

#mobile_money_section {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
}

#mobile_number_field {
    background-color: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
}
</style>
@endsection 