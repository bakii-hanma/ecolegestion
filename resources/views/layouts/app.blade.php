<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StudiaGabon - Gestion Scolaire')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #f8f9fa;
            --accent-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #343a40;
            --light-color: #ffffff;
            --border-radius: 0.5rem;
            --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --box-shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            line-height: 1.6;
        }

        /* Custom Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), #1e3a8a);
            box-shadow: var(--box-shadow-lg);
            border-bottom: 3px solid var(--accent-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--light-color) !important;
        }

        .navbar-brand:hover {
            color: var(--warning-color) !important;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        /* Offcanvas Sidebar */
        .offcanvas-custom {
            width: 300px !important;
            background: linear-gradient(180deg, #2c5aa0, #1e3a8a);
            color: var(--light-color);
        }

        .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
        }

        .offcanvas-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .nav-section {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-section:last-child {
            border-bottom: none;
        }

        .nav-section-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
            margin-bottom: 0.75rem;
            padding: 0 1.5rem;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--warning-color) !important;
            border-left-color: var(--warning-color);
            transform: translateX(5px);
        }

        .nav-link-custom.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: var(--warning-color) !important;
            border-left-color: var(--warning-color);
            font-weight: 600;
        }

        .nav-link-custom i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 76px);
            padding: 2rem 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), #1e3a8a);
            color: var(--light-color);
            border: none;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        /* Buttons */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            padding: 0.625rem 1.25rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1e3a8a);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-color), #20c997);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #fd7e14);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
        }

        /* Tables */
        .table {
            background: var(--light-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table tbody tr:hover {
            background-color: rgba(44, 90, 160, 0.05);
        }

        /* Progress bars */
        .progress {
            border-radius: 10px;
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem 0;
            }
            
            .stats-card .stats-icon {
                font-size: 2rem;
            }
        }

        /* Utilities */
        .text-primary-custom {
            color: var(--primary-color) !important;
        }

        .bg-primary-custom {
            background-color: var(--primary-color) !important;
        }

        .border-primary-custom {
            border-color: var(--primary-color) !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }

        /* Toast notifications */
        .toast {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-lg);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <i class="bi bi-list"></i>
            </button>
            
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard me-2"></i>
                StudiaGabon
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ Auth::user()->name ?? 'Utilisateur' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarLabel">
                <i class="bi bi-mortarboard me-2"></i>
                Menu Principal
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body p-0">
            <!-- Dashboard -->
            <div class="nav-section">
                <div class="nav-section-title">Tableau de bord</div>
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Accueil
                </a>
            </div>

            <!-- Gestion Académique -->
            <div class="nav-section">
                <div class="nav-section-title">Gestion Académique</div>
                <a href="{{ route('students.index') }}" class="nav-link-custom {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    Élèves
                </a>
                <a href="{{ route('teachers.index') }}" class="nav-link-custom {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i>
                    Enseignants
                </a>
                <a href="{{ route('parents.index') }}" class="nav-link-custom {{ request()->routeIs('parents.*') ? 'active' : '' }}">
                    <i class="bi bi-person-hearts"></i>
                    Parents
                </a>
                <a href="{{ route('classes.index') }}" class="nav-link-custom {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    Classes
                </a>
                <a href="{{ route('enrollments.index') }}" class="nav-link-custom {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    Inscriptions
                </a>
                <a href="{{ route('enrollments.pending-students') }}" class="nav-link-custom {{ request()->routeIs('enrollments.pending-students') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                    <i class="bi bi-clock-history"></i>
                    En attente
                </a>
            </div>

            <!-- Évaluations -->
            <div class="nav-section">
                <div class="nav-section-title">Évaluations</div>
                <a href="{{ route('subjects.index') }}" class="nav-link-custom {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    Matières
                </a>
                <a href="{{ route('grades.index') }}" class="nav-link-custom {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    Notes
                </a>
                <a href="{{ route('attendances.index') }}" class="nav-link-custom {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    Présences
                </a>
            </div>

            <!-- Finances -->
            <div class="nav-section">
                <div class="nav-section-title">Finances</div>
                <a href="{{ route('fees.index') }}" class="nav-link-custom {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    Frais scolaires
                </a>
                <a href="{{ route('payments.index') }}" class="nav-link-custom {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i>
                    Paiements
                </a>
            </div>

            <!-- Rapports -->
            <div class="nav-section">
                <div class="nav-section-title">Rapports</div>
                <a href="#" class="nav-link-custom">
                    <i class="bi bi-graph-up"></i>
                    Statistiques
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Bulletins
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="bi bi-printer"></i>
                    Impressions
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">StudiaGabon</strong>
                <small>À l'instant</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Action effectuée avec succès !
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global JavaScript functions
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Auto-close alerts after 5 seconds
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Add fade-in animation to cards
            var cards = document.querySelectorAll('.card');
            cards.forEach(function(card, index) {
                card.style.animationDelay = (index * 0.1) + 's';
                card.classList.add('fade-in-up');
            });
        });

        // Show toast notification
        function showToast(message, type = 'success') {
            var toastEl = document.getElementById('liveToast');
            var toastBody = toastEl.querySelector('.toast-body');
            var toastIcon = toastEl.querySelector('.toast-header i');
            
            toastBody.textContent = message;
            
            // Update icon based on type
            toastIcon.className = 'bi me-2';
            switch(type) {
                case 'success':
                    toastIcon.classList.add('bi-check-circle-fill', 'text-success');
                    break;
                case 'error':
                    toastIcon.classList.add('bi-exclamation-triangle-fill', 'text-danger');
                    break;
                case 'warning':
                    toastIcon.classList.add('bi-exclamation-circle-fill', 'text-warning');
                    break;
                default:
                    toastIcon.classList.add('bi-info-circle-fill', 'text-info');
            }
            
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Confirm delete action
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        // Format numbers
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
    </script>

    @stack('scripts')
</body>
</html>