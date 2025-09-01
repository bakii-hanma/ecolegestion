@extends('layouts.app')

@section('title', 'Détails de l\'Étudiant - ' . $student->first_name . ' ' . $student->last_name)

@section('head')
<style>
/* Styles pour la page de détails */
.page-header {
    background: white;
    padding: 25px 0;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.page-title {
    color: #2c3e50;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.page-title i {
    color: #007bff;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #0056b3;
}

.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 600;
}

.student-status-badge .badge {
    font-size: 14px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
}

.student-details-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.student-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 40px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.student-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.student-photo {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 5px solid rgba(255, 255, 255, 0.9);
    margin: 0 auto 20px;
    display: block;
    object-fit: cover;
    background: #f8f9fa;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
}

.student-name {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.student-matricule {
    font-size: 18px;
    opacity: 0.95;
    font-weight: 500;
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-block;
    backdrop-filter: blur(10px);
}

.student-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    padding: 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.info-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    border-left: 5px solid #007bff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
}

.info-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.info-section h4 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 20px;
    border-bottom: 2px solid #007bff;
    padding-bottom: 12px;
    font-weight: 600;
    position: relative;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.info-item:hover {
    background: rgba(0, 123, 255, 0.05);
    border-radius: 8px;
    padding-left: 10px;
    padding-right: 10px;
    margin-left: -10px;
    margin-right: -10px;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.info-label::before {
    content: '•';
    color: #007bff;
    font-weight: bold;
    margin-right: 8px;
    font-size: 18px;
}

.info-value {
    color: #2c3e50;
    text-align: right;
    font-weight: 500;
    background: rgba(0, 123, 255, 0.1);
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 13px;
    min-width: 80px;
    text-align: center;
}

.grades-summary {
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
    border-left-color: #28a745;
}

.grades-summary .info-item {
    border-bottom-color: #c3e6cb;
}

.grades-summary .info-value {
    background: rgba(40, 167, 69, 0.15);
    color: #155724;
}

.academic-info {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left-color: #ffc107;
}

.academic-info .info-item {
    border-bottom-color: #ffeaa7;
}

.academic-info .info-value {
    background: rgba(255, 193, 7, 0.15);
    color: #856404;
}

.action-buttons {
    text-align: center;
    padding: 40px 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 1px solid #dee2e6;
    position: relative;
}

.action-buttons::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #007bff, transparent);
}

.action-buttons .btn {
    margin: 0 8px;
    padding: 14px 28px;
    font-size: 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.action-buttons .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.action-buttons .btn:hover::before {
    left: 100%;
}

.action-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.action-buttons .btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.action-buttons .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
}

.action-buttons .btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
}

.action-buttons .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    border: none;
    color: #212529;
}

.action-buttons .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    border: none;
}

/* Modal styles - Optimisé pour A4 */
.bulletin-modal .modal-dialog {
    max-width: 98%;
    margin: 5px auto;
    height: 95vh;
}

.bulletin-modal .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.bulletin-modal .modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-bottom: none;
    padding: 20px 30px;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

.bulletin-modal .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.bulletin-modal .modal-title {
    font-size: 22px;
    font-weight: 700;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.bulletin-modal .modal-body {
    padding: 20px;
    flex: 1;
    overflow-y: auto;
    background: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.bulletin-modal .modal-footer {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 1px solid #dee2e6;
    padding: 15px 30px;
    position: relative;
    flex-shrink: 0;
}

.bulletin-modal .modal-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #007bff, transparent);
}

 /* Bulletin styles (pour le modal) - Format A4 */
.bulletin-page {
     background: white;
     margin: 0 auto;
     padding: 0;
     width: 210mm;
     min-height: 297mm;
     font-family: Arial, sans-serif;
     font-size: 11px;
     color: black;
     position: relative;
     border: 2px solid #333;
     box-shadow: 0 0 10px rgba(0,0,0,0.3);
     transform-origin: top center;
     transform: scale(0.8);
     margin-bottom: 20px;
 }

 /* HEADER EXACT SELON L'IMAGE - Format A4 */
.bulletin-header {
     display: flex;
     padding: 10px 15px;
     border-bottom: 2px solid black;
     align-items: flex-start;
     justify-content: space-between;
     background: #f9f9f9;
}

.header-left {
     display: flex;
     align-items: flex-start;
     flex: 1;
}

.republic-seal {
     width: 60px;
     height: 60px;
     margin-right: 15px;
     border: 1px solid black;
     display: flex;
     align-items: center;
     justify-content: center;
     background: white;
}

.republic-seal img {
     width: 100%;
     height: 100%;
     object-fit: cover;
}

.seal-placeholder {
     font-size: 6px;
     font-weight: bold;
     text-align: center;
     line-height: 1.1;
}

.year-info {
     flex: 1;
}

.ministry-line {
     font-size: 11px;
     font-weight: bold;
     margin-bottom: 3px;
}

.year-line {
     font-size: 11px;
     font-weight: bold;
}

.header-center {
     flex: 1;
     text-align: center;
}

.bulletin-title {
     font-size: 18px;
     font-weight: bold;
     text-align: center;
     margin: 15px 0;
     color: #2c3e50;
     text-transform: uppercase;
     letter-spacing: 1px;
}

.header-right {
     display: flex;
     align-items: flex-start;
     flex: 1;
     justify-content: flex-end;
}

.school-logo {
     margin-right: 15px;
}

.gabon-logo {
     width: 60px;
     height: 60px;
     border: 2px solid black;
     border-radius: 50%;
     background: white;
     display: flex;
     align-items: center;
     justify-content: center;
}

.logo-placeholder {
     font-size: 6px;
     font-weight: bold;
     text-align: center;
     line-height: 1.1;
}

.school-info {
     text-align: left;
}

.school-line {
     font-size: 13px;
     font-weight: bold;
     margin-bottom: 2px;
}

.contact-line {
     font-size: 9px;
}

 /* SECTION ÉTUDIANT EXACTE - TABLEAU AVEC BORDURES - Format A4 */
.student-info-table {
     width: 100%;
     border-collapse: collapse;
     margin: 10px 0;
     font-size: 10px;
     box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.student-info-table td {
     border: 1px solid black;
     padding: 4px;
     background: #f8f8f8;
     vertical-align: top;
}

.photo-cell {
     width: 80px;
     height: 100px;
     text-align: center;
     vertical-align: middle;
     background: white;
}

.photo-cell img {
     width: 100%;
     height: 100%;
     object-fit: cover;
}

.photo-placeholder {
     display: flex;
     align-items: center;
     justify-content: center;
     height: 100%;
     font-size: 8px;
     text-align: center;
     color: #666;
}

.name-cell {
     background: #f0f0f0;
     font-size: 12px;
     font-weight: bold;
     padding: 6px;
}

.info-cell {
     font-size: 9px;
     padding: 3px 5px;
     background: #f8f8f8;
 }

 /* TABLEAU EXACT SELON L'IMAGE - Format A4 */
.grades-section {
     padding: 15px;
     background: white;
     margin: 10px 0;
     border-radius: 5px;
     box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.grades-table {
     width: 100%;
     border-collapse: collapse;
     font-size: 9px;
     margin-bottom: 10px;
     box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.grades-table th {
     background: linear-gradient(135deg, #90EE90 0%, #7CFC00 100%);
     border: 1px solid #228B22;
     padding: 4px 3px;
     text-align: center;
     font-weight: bold;
     font-size: 8px;
     line-height: 1.2;
     color: #2c3e50;
}

.grades-table td {
     border: 1px solid #ddd;
     padding: 3px 2px;
     text-align: center;
     font-size: 9px;
     background: white;
}

.subject-cell {
     text-align: left;
     padding-left: 4px;
}

.green-triangle {
     color: #228B22;
     font-weight: bold;
     margin-right: 2px;
}

.totals-row {
     background: #f5f5f5;
     font-weight: bold;
}

.moyenne-trimestre {
     background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
     border: 2px solid #28a745;
     padding: 8px;
     text-align: center;
     font-weight: bold;
     margin-bottom: 10px;
     border-radius: 5px;
     color: #155724;
     font-size: 12px;
 }

 /* SECTIONS INFÉRIEURES EXACTES - Format A4 */
.bottom-sections {
     display: flex;
     padding: 0 15px;
     gap: 15px;
     margin-bottom: 15px;
}

.profil-section,
.bilan-section {
     flex: 1;
     border: 2px solid #333;
     padding: 10px;
     font-size: 10px;
     background: white;
     border-radius: 5px;
     box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-title {
     text-align: center;
     font-weight: bold;
     border-bottom: 2px solid #007bff;
     padding-bottom: 5px;
     margin-bottom: 8px;
     font-size: 12px;
     color: #2c3e50;
     text-transform: uppercase;
     letter-spacing: 0.5px;
}

.profil-item {
     display: flex;
     justify-content: space-between;
     margin-bottom: 2px;
     font-size: 8px;
}

.bilan-table {
     width: 100%;
     border-collapse: collapse;
     font-size: 7px;
}

.bilan-table th,
.bilan-table td {
     border: 1px solid black;
     padding: 2px;
     text-align: center;
}

.bilan-table th {
     background: linear-gradient(135deg, #90EE90 0%, #7CFC00 100%);
     font-weight: bold;
     color: #2c3e50;
     border: 1px solid #228B22;
 }

 /* DÉCISION EXACTE - Format A4 */
.decision-section {
     border: 2px solid #333;
     margin: 0 15px 15px 15px;
     padding: 15px;
     background: white;
     border-radius: 5px;
     box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.decision-title {
     text-align: center;
     font-weight: bold;
     font-size: 14px;
     margin-bottom: 10px;
     color: #2c3e50;
     text-transform: uppercase;
     letter-spacing: 1px;
     border-bottom: 2px solid #007bff;
     padding-bottom: 5px;
}

.decision-items {
     display: flex;
     justify-content: space-around;
     margin-bottom: 10px;
     font-size: 9px;
}

.admission-section {
     text-align: center;
}

.admission-badge {
     background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
     color: white;
     padding: 8px 25px;
     font-weight: bold;
     font-size: 16px;
     display: inline-block;
     border-radius: 25px;
     box-shadow: 0 2px 4px rgba(0,0,0,0.2);
     text-transform: uppercase;
     letter-spacing: 1px;
}

.decision-date {
     font-size: 9px;
     margin-top: 8px;
}

 /* FOOTER EXACT SELON L'IMAGE - Format A4 */
.footer-section {
     display: flex;
     justify-content: space-between;
     align-items: flex-end;
     padding: 20px 15px;
     border-top: 2px solid #333;
     min-height: 100px;
     background: #f9f9f9;
     border-radius: 0 0 5px 5px;
}

.left-seal-area,
.right-seal-area {
     flex: 1;
     text-align: center;
}

.center-area {
     flex: 2;
     text-align: center;
     display: flex;
     flex-direction: column;
     align-items: center;
}

.seal-title {
     font-size: 9px;
     font-weight: bold;
     margin-bottom: 10px;
}

.official-seal-left,
.official-seal-right {
     width: 60px;
     height: 60px;
     border: 2px solid black;
     border-radius: 50%;
     margin: 0 auto;
     display: flex;
     align-items: center;
     justify-content: center;
     background: white;
}

.seal-content {
     font-size: 6px;
     font-weight: bold;
     text-align: center;
     line-height: 1.1;
}

.qr-code-section {
     margin-bottom: 8px;
}

.qr-code-section img {
     width: 40px;
     height: 40px;
     border: 1px solid black;
}

.barcode-section {
     margin-bottom: 8px;
     text-align: center;
}

.barcode-section img {
     max-width: 200px;
     height: 50px;
     border: 1px solid #ccc;
     background: white;
     padding: 2px;
}

.bulletin-code {
     font-family: monospace;
     font-size: 10px;
     font-weight: bold;
     margin-bottom: 5px;
     text-align: center;
     color: #333;
}

.conseil-text {
     font-size: 8px;
     font-style: italic;
     color: #666;
     text-align: center;
     margin-top: 5px;
 }

 /* Effets visuels supplémentaires pour A4 */
 .bulletin-page::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     bottom: 0;
     background: linear-gradient(45deg, transparent 49%, rgba(0,123,255,0.02) 50%, transparent 51%);
     pointer-events: none;
     z-index: -1;
 }

 /* Amélioration des sceaux */
 .republic-seal,
 .gabon-logo,
 .official-seal-left,
 .official-seal-right {
     box-shadow: 0 2px 4px rgba(0,0,0,0.2);
     border: 2px solid #333 !important;
 }

 /* Amélioration du code-barre */
 .barcode-section {
     background: white;
     padding: 5px;
     border-radius: 3px;
     box-shadow: 0 1px 3px rgba(0,0,0,0.1);
 }

 .bulletin-code {
     background: #f8f9fa;
     padding: 3px 8px;
     border-radius: 3px;
     border: 1px solid #dee2e6;
     font-family: 'Courier New', monospace;
     font-weight: bold;
     color: #495057;
 }

/* Animations et effets */
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

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.student-details-card {
    animation: fadeInUp 0.6s ease-out;
}

.info-section {
    animation: slideInLeft 0.6s ease-out;
    animation-fill-mode: both;
}

.info-section:nth-child(1) { animation-delay: 0.1s; }
.info-section:nth-child(2) { animation-delay: 0.2s; }
.info-section:nth-child(3) { animation-delay: 0.3s; }

.action-buttons .btn {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.action-buttons .btn:nth-child(1) { animation-delay: 0.4s; }
.action-buttons .btn:nth-child(2) { animation-delay: 0.5s; }
.action-buttons .btn:nth-child(3) { animation-delay: 0.6s; }
.action-buttons .btn:nth-child(4) { animation-delay: 0.7s; }
.action-buttons .btn:nth-child(5) { animation-delay: 0.8s; }

.student-photo:hover {
    animation: pulse 0.6s ease-in-out;
}

/* Responsive design amélioré */
@media (max-width: 768px) {
    .student-info-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px;
    }
    
    .action-buttons .btn {
        margin: 5px;
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .student-header {
        padding: 30px 20px;
    }
    
    .student-name {
        font-size: 24px;
    }
    
    .student-photo {
        width: 120px;
        height: 120px;
    }
    
    .page-title {
        font-size: 22px;
    }
}

@media (max-width: 576px) {
    .action-buttons {
        padding: 20px 15px;
    }
    
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
    
    .info-section {
        padding: 20px;
    }
    
    .student-header {
        padding: 25px 15px;
    }
}

</style>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('grades.index') }}">Notes</a></li>
<li class="breadcrumb-item active">Détails - {{ $student->first_name }} {{ $student->last_name }}</li>
@endsection

@section('content')
<!-- En-tête de la page avec titre -->
<div class="page-header mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-user-graduate text-primary me-3"></i>
                    Détails de l'Étudiant
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('grades.index') }}"><i class="fas fa-chart-line"></i> Notes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-user"></i> {{ $student->first_name }} {{ $student->last_name }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="student-status-badge">
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Actif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="student-details-card">
    <!-- Header avec photo et nom -->
    <div class="student-header">
        @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
            <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo {{ $student->first_name }}" class="student-photo">
        @elseif($student->avatar)
            <img src="{{ $student->avatar }}" alt="Photo {{ $student->first_name }}" class="student-photo">
        @else
            <div class="student-photo" style="display: flex; align-items: center; justify-content: center; font-size: 48px; color: #ccc;">
                <i class="fas fa-user"></i>
            </div>
        @endif
        <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
        <div class="student-matricule">{{ $studentInfo['matricule'] ?? $student->student_id }}</div>
    </div>

    <!-- Grille d'informations -->
    <div class="student-info-grid">
        <!-- Informations personnelles -->
        <div class="info-section">
            <h4><i class="fas fa-user-circle"></i> Informations Personnelles</h4>
            <div class="info-item">
                <span class="info-label">Date de naissance</span>
                <span class="info-value">{{ $studentInfo['birth_date'] ?? 'N/C' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Lieu de naissance</span>
                <span class="info-value">{{ $studentInfo['birth_place'] ?? 'N/C' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Sexe</span>
                <span class="info-value">{{ $studentInfo['gender'] ?? 'N/C' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nationalité</span>
                <span class="info-value">{{ $studentInfo['nationality'] ?? 'Gabonaise' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Âge</span>
                <span class="info-value">{{ $student->age ?? 'N/C' }} ans</span>
            </div>
        </div>

        <!-- Informations académiques -->
        <div class="info-section academic-info">
            <h4><i class="fas fa-graduation-cap"></i> Informations Académiques</h4>
            <div class="info-item">
                <span class="info-label">Classe actuelle</span>
                <span class="info-value">{{ $class->name ?? 'N/C' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Niveau</span>
                <span class="info-value">{{ $class->level->name ?? 'N/C' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Effectif de la classe</span>
                <span class="info-value">{{ $totalStudents ?? 'N/C' }} élèves</span>
            </div>
            <div class="info-item">
                <span class="info-label">Année scolaire</span>
                <span class="info-value">{{ $academicYear->name ?? '2024-2025' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Professeur principal</span>
                <span class="info-value">{{ $principalTeacherName ?? 'N/C' }}</span>
            </div>
        </div>

        <!-- Résumé des notes -->
        <div class="info-section grades-summary">
            <h4><i class="fas fa-chart-line"></i> Résumé des Notes</h4>
            @if(!empty($trimesterData))
                @php $lastTrimester = array_key_last($trimesterData); @endphp
                <div class="info-item">
                    <span class="info-label">Moyenne trimestrielle</span>
                    <span class="info-value">{{ $trimesterData[$lastTrimester]['cumulative_score'] > 0 ? $trimesterData[$lastTrimester]['cumulative_score'] : 'N/C' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rang dans la classe</span>
                    <span class="info-value">{{ $trimesterData[$lastTrimester]['rank'] ?? 'N/C' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Moyenne de la classe</span>
                    <span class="info-value">{{ $classProfile['moyenne_classe'] ?? 'N/C' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nombre de matières</span>
                    <span class="info-value">{{ count($trimesterData[$lastTrimester]['subjects'] ?? []) }}</span>
                </div>
            @else
                <div class="info-item">
                    <span class="info-label">Aucune note disponible</span>
                    <span class="info-value">-</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Boutons d'action -->
<div class="action-buttons">
        <button onclick="showBulletin()" class="btn btn-primary">
            <i class="fas fa-eye"></i> Voir le Bulletin
        </button>
        <a href="{{ route('grades.create', ['student_id' => $student->id]) }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Ajouter une note
    </a>
        <a href="{{ route('grades.manage-student', $student->id) }}" class="btn btn-info">
        <i class="fas fa-edit"></i> Gérer les notes
    </a>
        <button onclick="generatePDF()" class="btn btn-warning">
        <i class="fas fa-download"></i> Télécharger PDF
    </button>
        <button onclick="printPDF()" class="btn btn-secondary">
         <i class="fas fa-print"></i> Imprimer PDF
     </button>
</div>
</div>
    <!-- Modal pour l'aperçu du bulletin -->
<div class="modal fade bulletin-modal" id="bulletinModal" tabindex="-1" aria-labelledby="bulletinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulletinModalLabel">
                    <i class="fas fa-file-alt"></i> Bulletin de {{ $student->first_name }} {{ $student->last_name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
<div class="bulletin-page">
    <!-- HEADER EXACT SELON L'IMAGE -->
    <div class="bulletin-header">
        <div class="header-left">
            <div class="school-logo">
                                @if($schoolSettings && $schoolSettings->school_logo)
                                    <img src="{{ $schoolSettings->logo_url }}" alt="Logo {{ $schoolSettings->school_name }}" style="max-height: 60px; max-width: 80px;">
                                @else
                <div class="gabon-logo">
                    <div class="logo-placeholder">LOGO<br>ÉCOLE</div>
                </div>
                                @endif
            </div>
            <div class="school-info">
                                <div class="school-line">{{ $schoolSettings->school_name ?? 'Lycée XXXXX' }}</div>
                                <div class="contact-line">{{ $schoolSettings->school_bp ?? 'BP: 6' }}, Téléphone: {{ $schoolSettings->school_phone ?? '06037499' }}</div>
            </div>
        </div>
        <div class="header-center">
            @php
                $currentMonth = date('n');
                if ($currentMonth >= 9 || $currentMonth <= 12) {
                    $currentTerm = 1;
                    $termLabel = '1er TRIMESTRE';
                } elseif ($currentMonth >= 1 && $currentMonth <= 3) {
                    $currentTerm = 2;
                    $termLabel = '2ème TRIMESTRE';
                } else {
                    $currentTerm = 3;
                    $termLabel = '3ème TRIMESTRE';
                }
            @endphp
            <div class="bulletin-title">BULLETIN - {{ $termLabel }}</div>
        </div>
        <div class="header-right">
            <div class="republic-seal">
                                @if($schoolSettings && $schoolSettings->school_seal)
                                    <img src="{{ $schoolSettings->seal_url }}" alt="Sceau {{ $schoolSettings->school_name }}" style="max-height: 60px; max-width: 80px;">
                                @elseif(file_exists(public_path('sceau-221128112237.png')))
                    <img src="{{ asset('sceau-221128112237.png') }}" alt="Sceau République Gabonaise">
                @else
                    <div class="seal-placeholder">SCEAU<br>RÉPUBLIQUE<br>GABONAISE</div>
                @endif
            </div>
            <div class="year-info">
                <div class="ministry-line">Ministère de l'Education Nationale</div>
                                <div class="year-line">Année Scolaire : {{ $schoolSettings->academic_year ?? ($academicYear->name ?? '2024-2025') }}</div>
            </div>
        </div>
    </div>

    <!-- SECTION ÉTUDIANT EXACTE - TABLEAU AVEC BORDURES -->
    <table class="student-info-table">
        <tr>
            <td rowspan="3" class="photo-cell">
                @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo {{ $student->first_name }}">
                @elseif($student->avatar)
                    <img src="{{ $student->avatar }}" alt="Photo {{ $student->first_name }}">
                @else
                    <div class="photo-placeholder">Photo<br>de<br>l'élève</div>
                @endif
            </td>
                         <td colspan="5" class="name-cell">
                 <strong>{{ strtoupper($studentInfo['last_name']) }} {{ $studentInfo['first_name'] }} X [{{ $studentInfo['matricule'] }}]</strong>
             </td>
         </tr>
         <tr>
             <td class="info-cell"><strong>Né(e) le :</strong> {{ $studentInfo['birth_date'] }}</td>
             <td class="info-cell"><strong>Lieu de naissance:</strong> {{ $studentInfo['birth_place'] }}</td>
             <td class="info-cell"><strong>Sexe :</strong> {{ $studentInfo['gender'] }} | Statut: [T]</td>
         </tr>
         <tr>
             <td class="info-cell"><strong>Classe :</strong> {{ $class->name ?? 'N/C' }}</td>
             <td class="info-cell"><strong>Effectif :</strong> {{ $totalStudents ?? 'N/C' }} Masculin: {{ $maleStudents ?? 'N/C' }} | Féminin: {{ $femaleStudents ?? 'N/C' }}</td>
             <td class="info-cell"><strong>Nationalité :</strong> {{ $studentInfo['nationality'] }}</td>
        </tr>
    </table>

    <!-- TABLEAU EXACT -->
    <div class="grades-section">
        <table class="grades-table">
            <thead>
                <tr>
                    <th>DISCIPLINES</th>
                    <th>MOYENNE<br>Apprenant</th>
                    <th>MOYENNE<br>Classe</th>
                    <th>COEF</th>
                    <th>NOTE X<br>COEF</th>
                    <th>RANG</th>
                    <th>ABSENCES</th>
                    <th>Appréciation</th>
                    <th>Professeur</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($trimesterData))
                    @php
                        $lastTrimester = array_key_last($trimesterData);
                        $currentTrimesterGrades = $trimesterData[$lastTrimester];
                        $totalCoeff = 0;
                        $totalNoteCoeff = 0;
                    @endphp
                    
                    @foreach($currentTrimesterGrades['subjects'] as $subjectData)
                        @php
                            $coefficient = $subjectCoefficients[strtolower($subjectData['subject_name'])] ?? 1;
                            $noteCoeff = $subjectData['average'] > 0 ? $subjectData['average'] * $coefficient : 0;
                            $rank = $subjectRanks[$subjectData['subject_id']] ?? 'N/C';
                            $classAverage = $classAverages[$subjectData['subject_id']] ?? 'N/C';
                            
                            $totalCoeff += $coefficient;
                            $totalNoteCoeff += $noteCoeff;
                        @endphp
                        <tr>
                            <td class="subject-cell">{{ $subjectData['subject_name'] }}</td>
                            <td><span class="green-triangle">▲</span> {{ $subjectData['average'] > 0 ? $subjectData['average'] : 'N/C' }}</td>
                            <td>{{ $classAverage }}</td>
                            <td>{{ $coefficient }}</td>
                            <td>{{ $subjectData['average'] > 0 ? number_format($noteCoeff, 1) : 'N/C' }}</td>
                            <td>{{ $rank }}</td>
                            <td>0h00</td>
                            <td>
                                @if($subjectData['average'] > 0)
                                    @if($subjectData['average'] >= 16) Excellent
                                    @elseif($subjectData['average'] >= 14) Très bien
                                    @elseif($subjectData['average'] >= 12) Bien
                                    @elseif($subjectData['average'] >= 10) Assez bien
                                    @elseif($subjectData['average'] >= 8) Passable
                                    @else Insuffisant
                                    @endif
                                @else
                                    N/C
                                @endif
                            </td>
                            <td>{{ $subjectData['teacher_name'] }}</td>
                        </tr>
                    @endforeach
                    
                    <tr class="totals-row">
                        <td class="subject-cell"><strong>TOTAUX</strong></td>
                        <td><strong>{{ $currentTrimesterGrades['cumulative_score'] > 0 ? $currentTrimesterGrades['cumulative_score'] : 'N/C' }}</strong></td>
                        <td><strong>{{ $totalCoeff }}</strong></td>
                        <td><strong>{{ number_format($totalNoteCoeff, 1) }}</strong></td>
                        <td>{{ $currentTrimesterGrades['rank'] ?? 'N/C' }}</td>
                        <td>0h00</td>
                        <td colspan="3"><strong>Moyenne trimestrielle: {{ $currentTrimesterGrades['cumulative_score'] > 0 ? $currentTrimesterGrades['cumulative_score'] : 'N/C' }}</strong> <span class="green-triangle">▲</span></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px;">Aucune note disponible</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- MOYENNE TRIMESTRIELLE -->
        <div class="moyenne-trimestre">
            @if(!empty($trimesterData))
                @php $lastTrimester = array_key_last($trimesterData); @endphp
                Moyenne trimestrielle: {{ $trimesterData[$lastTrimester]['cumulative_score'] > 0 ? $trimesterData[$lastTrimester]['cumulative_score'] : 'N/C' }} <span class="green-triangle">▲</span>
            @else
                Moyenne trimestrielle: N/C
            @endif
        </div>
    </div>

    <!-- SECTIONS INFÉRIEURES EXACTES -->
    <div class="bottom-sections">
        <!-- PROFIL DE LA CLASSE -->
        <div class="profil-section">
            <div class="section-title">PROFIL DE LA CLASSE</div>
            <div class="profil-item">
                <span>Forte moyenne trim</span>
                <span>{{ $classProfile['meilleure_note'] ?? 'N/C' }}</span>
            </div>
            <div class="profil-item">
                <span>Faible moyenne trim</span>
                <span>{{ $classProfile['plus_basse_note'] ?? 'N/C' }}</span>
            </div>
            <div class="profil-item">
                <span>Moyenne de la classe</span>
                <span>{{ $classProfile['moyenne_classe'] ?? 'N/C' }}</span>
            </div>
            <div class="profil-item" style="margin-top: 8px;">
                <span><strong>PROFESSEUR</strong></span>
                <span><strong>PROFESSEUR PRINCIPAL</strong></span>
            </div>
            <div class="profil-item">
                <span><strong>PRINCIPAL</strong></span>
                 <span><strong>{{ $principalTeacherName }}</strong></span>
            </div>
        </div>

        <!-- BILAN -->
        <div class="bilan-section">
            <div class="section-title">BILAN</div>
            <table class="bilan-table">
                <thead>
                    <tr>
                        <th>Moyenne</th>
                        <th>Apprenant</th>
                        <th>Classe Rang</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($trimesterData))
                        @foreach($trimesterData as $trimester => $data)
                            <tr>
                                <td>{{ $trimester }}</td>
                                <td>{{ $data['cumulative_score'] > 0 ? $data['cumulative_score'] : 'N/C' }} 
                                    @if($loop->index > 0 && $data['cumulative_score'] > $trimesterData[array_keys($trimesterData)[$loop->index - 1]]['cumulative_score'])
                                        <span class="green-triangle">▲</span>
                                    @endif
                                </td>
                                <td>{{ $classProfile['moyenne_classe'] ?? 'N/C' }} {{ $data['rank'] ?? 'N/C' }}</td>
                            </tr>
                        @endforeach
                        
                        @if(count($trimesterData) > 1)
                            <tr style="font-weight: bold;">
                                <td>MOYENNE</td>
                                <td>{{ $generalBalance['moyenne_generale'] ?? 'N/C' }} {{ $generalBalance['evolution'] ?? 'N/C' }}</td>
                                <td>{{ $classProfile['moyenne_classe'] ?? 'N/C' }} {{ $generalBalance['rank'] ?? 'N/C' }}</td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td colspan="3">Aucune note disponible</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3"><strong>ANNUELLE:</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DÉCISION EXACTE -->
    <div class="decision-section">
        <div class="decision-title">DECISION DU CONSEIL DE CLASSE</div>
        <div class="decision-items">
              <span>Conduite : <span id="conduite-note">NC</span> <span id="conduite-button" style="display: none;"><button onclick="editConduite()" class="btn btn-sm btn-outline-primary" style="font-size: 8px; padding: 1px 3px;">Modifier</button></span></span>
            <span>Travail : Assez Bien/TH</span>
            <span>Fréquentation : A suivre</span>
        </div>
        <div class="admission-section">
            <div class="admission-badge">Admis(e)</div>
            <div class="decision-date">05-01-2017</div>
        </div>
    </div>

         <!-- FOOTER CORRIGÉ - CODE-BARRE À GAUCHE, CACHET À DROITE -->
     <div class="footer-section">
         <div class="left-seal-area">
             @php
                 // Générer un matricule bulletin unique de 12 chiffres
                 $bulletinMatricule = date('Y') . str_pad($studentInfo['id'], 4, '0', STR_PAD_LEFT) . str_pad(substr(time(), -4), 4, '0', STR_PAD_LEFT);
             @endphp
             <div class="barcode-section">
                 <img src="https://quickchart.io/barcode?type=code128&text={{ $bulletinMatricule }}&includeText=true&width=3&height=50" 
                      alt="Code Barre" 
                      style="max-width: 200px; height: 50px; border: 1px solid #ccc;"
                      onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjUwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNmMGYwZjAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9Im1vbm9zcGFjZSIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzMzMyIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPio8L3RleHQ+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJtb25vc3BhY2UiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiMzMzMiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuOWVtIj57{ $bulletinMatricule }8PC90ZXh0Pjwvc3ZnPg=='">
             </div>
             <div class="bulletin-code" style="font-size: 10px; margin-top: 5px;">*{{ $bulletinMatricule }}*</div>
         </div>
         
         <div class="center-area">
             <div class="conseil-text">* Conseil de Classe</div>
         </div>
         
         <div class="right-seal-area">
             <div class="seal-title">Le Proviseur,</div>
             <div class="official-seal-right">
                 <!-- Zone blanche pour le cachet -->
             </div>
         </div>
                      </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                     <i class="fas fa-times"></i> Fermer
                 </button>
                 <button onclick="generatePDF()" class="btn btn-success">
                     <i class="fas fa-download"></i> Télécharger PDF
                 </button>
                 <button onclick="printPDF()" class="btn btn-info">
                     <i class="fas fa-print"></i> Imprimer PDF
                 </button>
                 <button onclick="printHTML()" class="btn btn-warning">
                     <i class="fas fa-print"></i> Imprimer HTML
                 </button>
             </div>
         </div>
     </div>
</div>

<!-- Scripts pour PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
 // Fonction pour afficher le bulletin dans le modal
 function showBulletin() {
     const modal = new bootstrap.Modal(document.getElementById('bulletinModal'));
     modal.show();
 }
 
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Configuration
    const pageWidth = doc.internal.pageSize.width;
    const margin = 10;
    let yPos = margin;
    
    // Bordure de page
    doc.setDrawColor(0, 0, 0);
    doc.setLineWidth(0.5);
    doc.rect(margin, margin, pageWidth - 2 * margin, 277);
    
    // Header
    yPos = margin + 5;
    
    // Logo Gabon (cercle)
    doc.circle(margin + 15, yPos + 12, 10);
    doc.setFontSize(6);
    doc.text('ARMOIRIES', margin + 15, yPos + 10, { align: 'center' });
    doc.text('GABON', margin + 15, yPos + 14, { align: 'center' });
    
    // Ministère et école
    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text('Ministère de l\'Education Nationale', margin + 30, yPos + 5);
    doc.setFontSize(13);
    doc.text('{{ $schoolSettings->school_name ?? "Lycée XXXXX" }}', margin + 30, yPos + 12);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('{{ $schoolSettings->school_bp ?? "BP: 6" }}, Téléphone: {{ $schoolSettings->school_phone ?? "06037499" }}', margin + 30, yPos + 18);
    
    // Titre centré
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('BULLETIN - ' + document.querySelector('.bulletin-title').textContent.split(' - ')[1], pageWidth / 2, yPos + 30, { align: 'center' });
    
    // Année scolaire et sceau
    doc.setFontSize(11);
    doc.text(document.querySelector('.year-line').textContent, pageWidth - margin - 5, yPos + 5, { align: 'right' });
    doc.circle(pageWidth - margin - 15, yPos + 18, 8);
    doc.setFontSize(5);
    doc.text('RÉPUBLIQUE', pageWidth - margin - 15, yPos + 14, { align: 'center' });
    doc.text('GABONAISE', pageWidth - margin - 15, yPos + 16, { align: 'center' });
    doc.text('★ ★', pageWidth - margin - 15, yPos + 19, { align: 'center' });
    doc.text('UNION • TRAVAIL • JUSTICE', pageWidth - margin - 15, yPos + 22, { align: 'center' });
    
    yPos += 45;
    
    // Section étudiant (gris)
    doc.setFillColor(232, 232, 232);
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 25, 'F');
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 25, 'D');
    
    // Photo placeholder
    doc.setFillColor(255, 255, 255);
    doc.rect(margin + 8, yPos + 3, 18, 19, 'FD');
    doc.setFontSize(6);
    doc.text('Photo', margin + 17, yPos + 10, { align: 'center' });
    doc.text('de l\'élève', margin + 17, yPos + 15, { align: 'center' });
    
         // Nom et infos étudiant
     // Utiliser les variables globales déjà chargées
     if (!studentData || !studentInfo) {
         console.error('Données étudiant non disponibles pour le PDF');
         return;
     }
     
     // Données du profil de classe pour le PDF
     const classProfileData = {
         forteMoyenne: '{{ $classProfile["meilleure_note"] ?? "N/C" }}',
         faibleMoyenne: '{{ $classProfile["plus_basse_note"] ?? "N/C" }}',
         moyenneClasse: '{{ $classProfile["moyenne_classe"] ?? "N/C" }}',
         principalTeacher: principalTeacher
     };
     
     doc.setFontSize(14);
     doc.setFont('helvetica', 'bold');
     doc.text(`${studentInfo.last_name.toUpperCase()} ${studentInfo.first_name} X [${studentInfo.matricule}]`, margin + 30, yPos + 8);
     
     doc.setFontSize(8);
     doc.setFont('helvetica', 'normal');
     doc.text(`Né(e) le : ${studentInfo.birth_date}     Lieu de naissance: ${studentInfo.birth_place}     Sexe : ${studentInfo.gender} | Statut: [T]`, margin + 8, yPos + 15);
     doc.text(`Classe : ${studentData.school_class?.name || 'N/C'}     Effectif : ${studentData.total_students || 'N/C'} Masculin: ${studentData.male_students || 'N/C'} | Féminin: ${studentData.female_students || 'N/C'}     Nationalité : ${studentInfo.nationality}`, margin + 8, yPos + 20);
    
    yPos += 35;
    
    // Tableau des notes
    const tableHeaders = [
        'DISCIPLINES',
        'MOYENNE\nApprenant',
        'MOYENNE\nClasse',
        'COEF',
        'NOTE X\nCOEF',
        'RANG',
        'ABSENCES',
        'Appréciation',
        'Professeur'
    ];
    
    // Récupérer les données du tableau HTML
    const tableData = [];
    document.querySelectorAll('.grades-table tbody tr:not(.totals-row)').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 9) {
            tableData.push([
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim(),
                cells[7].textContent.trim(),
                cells[8].textContent.trim()
            ]);
        }
    });
    
    // Ligne totaux
    const totalsRow = document.querySelector('.grades-table .totals-row');
    if (totalsRow) {
        const cells = totalsRow.querySelectorAll('td');
        if (cells.length >= 6) {
            tableData.push([
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim() + (cells[7] ? ' ' + cells[7].textContent.trim() : '') + (cells[8] ? ' ' + cells[8].textContent.trim() : ''),
                '',
                ''
            ]);
        }
    }
    
    doc.autoTable({
        startY: yPos,
        head: [tableHeaders],
        body: tableData,
        theme: 'grid',
        styles: {
            fontSize: 6,
            cellPadding: 1,
            lineColor: [0, 0, 0],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [144, 238, 144], // Vert exact
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        columnStyles: {
            0: { cellWidth: 22, halign: 'left' },
            1: { cellWidth: 16 },
            2: { cellWidth: 16 },
            3: { cellWidth: 10 },
            4: { cellWidth: 16 },
            5: { cellWidth: 10 },
            6: { cellWidth: 14 },
            7: { cellWidth: 18 },
            8: { cellWidth: 28 }
        }
    });
    
    yPos = doc.lastAutoTable.finalY + 5;
    
    // Moyenne trimestrielle
    doc.setFillColor(232, 245, 232);
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 8, 'F');
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 8, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('Moyenne trimestrielle: 12.03 ▲', pageWidth / 2, yPos + 5, { align: 'center' });
    
    yPos += 15;
    
    // Profil et Bilan
    const sectionWidth = (pageWidth - 2 * margin - 15) / 2;
    
    // Profil de la classe
    doc.rect(margin + 5, yPos, sectionWidth, 30, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('PROFIL DE LA CLASSE', margin + 5 + sectionWidth/2, yPos + 6, { align: 'center' });
    doc.line(margin + 8, yPos + 8, margin + sectionWidth - 2, yPos + 8);
    
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');
    const profilItems = [
         ['Forte moyenne trim', classProfileData.forteMoyenne],
         ['Faible moyenne trim', classProfileData.faibleMoyenne],
         ['Moyenne de la classe', classProfileData.moyenneClasse],
         ['PROFESSEUR', 'PROFESSEUR PRINCIPAL'],
         ['PRINCIPAL', classProfileData.principalTeacher]
    ];
    
    let profilY = yPos + 12;
    profilItems.forEach(item => {
        doc.text(item[0], margin + 8, profilY);
        doc.text(item[1], margin + sectionWidth - 8, profilY, { align: 'right' });
        profilY += 3;
    });
    
    // Bilan
    const bilanX = margin + sectionWidth + 10;
    doc.rect(bilanX, yPos, sectionWidth, 30, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('BILAN', bilanX + sectionWidth/2, yPos + 6, { align: 'center' });
    
    const bilanData = [
        ['1er Trimestre', '7.28', '8.89 44'],
        ['2ème Trimestre', '9.1 ▲', '8.61 24'],
        ['3ème Trimestre', '12.03 ▲', '10.79 18'],
        ['MOYENNE ANNUELLE:', '9.47 +0.09 (CC*)=10.00', '9.42 23']
    ];
    
    doc.autoTable({
        startY: yPos + 9,
        head: [['Moyenne', 'Apprenant', 'Classe Rang']],
        body: bilanData,
        theme: 'grid',
        styles: {
            fontSize: 5,
            cellPadding: 1
        },
        headStyles: {
            fillColor: [144, 238, 144],
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        margin: { left: bilanX + 2, right: margin + 5 },
        tableWidth: sectionWidth - 4
    });
    
    yPos += 40;
    
    // Décision du conseil
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 20, 'D');
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('DECISION DU CONSEIL DE CLASSE', pageWidth / 2, yPos + 6, { align: 'center' });
    
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Conduite : NC', margin + 20, yPos + 12);
    doc.text('Travail : Assez Bien/TH', pageWidth / 2, yPos + 12, { align: 'center' });
    doc.text('Fréquentation : A suivre', pageWidth - margin - 40, yPos + 12);
    
    // Badge Admis
    doc.setFillColor(0, 0, 0);
    doc.rect(pageWidth / 2 - 12, yPos + 14, 24, 5, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold');
    doc.text('Admis(e)', pageWidth / 2, yPos + 17, { align: 'center' });
    doc.setTextColor(0, 0, 0);
    
    yPos += 25;
    
    // Footer
    doc.line(margin + 5, yPos, pageWidth - margin - 5, yPos);
    yPos += 5;
    
         // Code-barre à gauche
     doc.rect(margin + 5, yPos + 3, 35, 8, 'D');
     doc.text('||||||||||||||||||||||||||||||||', margin + 22, yPos + 7, { align: 'center' });
     
     doc.setFontSize(8);
     doc.text(`*${String(studentData.id).padStart(16, '0')}*`, margin + 22, yPos + 15, { align: 'center' });
     
     // Signature à droite
     doc.setFontSize(8);
     doc.setFont('helvetica', 'bold');
     doc.text('Le Proviseur,', pageWidth - margin - 25, yPos + 3);
     doc.circle(pageWidth - margin - 25, yPos + 12, 6);
     // Zone blanche pour le cachet (pas de texte)
     
     // Conseil de classe au centre
     doc.setFontSize(6);
     doc.text('* Conseil de Classe', pageWidth / 2, yPos + 25, { align: 'center' });
    
    // Date
    doc.setFontSize(7);
    doc.text('05-01-2017', pageWidth / 2, yPos + 25, { align: 'center' });
    
    doc.save(`bulletin-${studentData.first_name}-${studentData.last_name}.pdf`);
}

function printPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Configuration
    const pageWidth = doc.internal.pageSize.width;
    const margin = 10;
    let yPos = margin;
    
    // Bordure de page
    doc.setDrawColor(0, 0, 0);
    doc.setLineWidth(0.5);
    doc.rect(margin, margin, pageWidth - 2 * margin, 277);
    
    // Header
    yPos = margin + 5;
    
    // Logo Gabon (cercle)
    doc.circle(margin + 15, yPos + 12, 10);
    doc.setFontSize(6);
    doc.text('ARMOIRIES', margin + 15, yPos + 10, { align: 'center' });
    doc.text('GABON', margin + 15, yPos + 14, { align: 'center' });
    
    // Ministère et école
    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text('Ministère de l\'Education Nationale', margin + 30, yPos + 5);
    doc.setFontSize(13);
    doc.text('{{ $schoolSettings->school_name ?? "Lycée XXXXX" }}', margin + 30, yPos + 12);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('{{ $schoolSettings->school_bp ?? "BP: 6" }}, Téléphone: {{ $schoolSettings->school_phone ?? "06037499" }}', margin + 30, yPos + 18);
    
    // Titre centré
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('BULLETIN - ' + document.querySelector('.bulletin-title').textContent.split(' - ')[1], pageWidth / 2, yPos + 30, { align: 'center' });
    
    // Année scolaire et sceau
    doc.setFontSize(11);
    doc.text(document.querySelector('.year-line').textContent, pageWidth - margin - 5, yPos + 5, { align: 'right' });
    doc.circle(pageWidth - margin - 15, yPos + 18, 8);
    doc.setFontSize(5);
    doc.text('RÉPUBLIQUE', pageWidth - margin - 15, yPos + 14, { align: 'center' });
    doc.text('GABONAISE', pageWidth - margin - 15, yPos + 16, { align: 'center' });
    doc.text('★ ★', pageWidth - margin - 15, yPos + 19, { align: 'center' });
    doc.text('UNION • TRAVAIL • JUSTICE', pageWidth - margin - 15, yPos + 22, { align: 'center' });
    
    yPos += 45;
    
    // Section étudiant (gris)
    doc.setFillColor(232, 232, 232);
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 25, 'F');
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 25, 'D');
    
    // Photo placeholder
    doc.setFillColor(255, 255, 255);
    doc.rect(margin + 8, yPos + 3, 18, 19, 'FD');
    doc.setFontSize(6);
    doc.text('Photo', margin + 17, yPos + 10, { align: 'center' });
    doc.text('de l\'élève', margin + 17, yPos + 15, { align: 'center' });
    
    // Nom et infos étudiant
    // Utiliser les variables globales déjà chargées
    if (!studentData || !studentInfo) {
        console.error('Données étudiant non disponibles pour l\'impression');
        return;
    }
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text(`${studentInfo.last_name.toUpperCase()} ${studentInfo.first_name} X [${studentInfo.matricule}]`, margin + 30, yPos + 8);
    
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text(`Né(e) le : ${studentInfo.birth_date}     Lieu de naissance: ${studentInfo.birth_place}     Sexe : ${studentInfo.gender} | Statut: [T]`, margin + 8, yPos + 15);
    doc.text(`Classe : ${studentData.school_class?.name || 'N/C'}     Effectif : ${studentData.total_students || 'N/C'} Masculin: ${studentData.male_students || 'N/C'} | Féminin: ${studentData.female_students || 'N/C'}     Nationalité : ${studentInfo.nationality}`, margin + 8, yPos + 20);
    
    yPos += 35;
    
    // Tableau des notes
    const tableHeaders = [
        'DISCIPLINES',
        'MOYENNE\nApprenant',
        'MOYENNE\nClasse',
        'COEF',
        'NOTE X\nCOEF',
        'RANG',
        'ABSENCES',
        'Appréciation',
        'Professeur'
    ];
    
    // Récupérer les données du tableau HTML
    const tableData = [];
    document.querySelectorAll('.grades-table tbody tr:not(.totals-row)').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 9) {
            tableData.push([
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim(),
                cells[7].textContent.trim(),
                cells[8].textContent.trim()
            ]);
        }
    });
    
    // Ligne totaux
    const totalsRow = document.querySelector('.grades-table .totals-row');
    if (totalsRow) {
        const cells = totalsRow.querySelectorAll('td');
        if (cells.length >= 6) {
            tableData.push([
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim() + (cells[7] ? ' ' + cells[7].textContent.trim() : '') + (cells[8] ? ' ' + cells[8].textContent.trim() : ''),
                '',
                ''
            ]);
        }
    }
    
    doc.autoTable({
        startY: yPos,
        head: [tableHeaders],
        body: tableData,
        theme: 'grid',
        styles: {
            fontSize: 6,
            cellPadding: 1,
            lineColor: [0, 0, 0],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [144, 238, 144], // Vert exact
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        columnStyles: {
            0: { cellWidth: 22, halign: 'left' },
            1: { cellWidth: 16 },
            2: { cellWidth: 16 },
            3: { cellWidth: 10 },
            4: { cellWidth: 16 },
            5: { cellWidth: 10 },
            6: { cellWidth: 14 },
            7: { cellWidth: 18 },
            8: { cellWidth: 28 }
        }
    });
    
    yPos = doc.lastAutoTable.finalY + 5;
    
    // Moyenne trimestrielle
    doc.setFillColor(232, 245, 232);
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 8, 'F');
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 8, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('Moyenne trimestrielle: 12.03 ▲', pageWidth / 2, yPos + 5, { align: 'center' });
    
    yPos += 15;
    
    // Profil et Bilan
    const sectionWidth = (pageWidth - 2 * margin - 15) / 2;
    
    // Profil de la classe
    doc.rect(margin + 5, yPos, sectionWidth, 30, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('PROFIL DE LA CLASSE', margin + 5 + sectionWidth/2, yPos + 6, { align: 'center' });
    doc.line(margin + 8, yPos + 8, margin + sectionWidth - 2, yPos + 8);
    
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');
    
    // Données du profil de classe pour l'impression
    const classProfileData = {
        forteMoyenne: '{{ $classProfile["meilleure_note"] ?? "N/C" }}',
        faibleMoyenne: '{{ $classProfile["plus_basse_note"] ?? "N/C" }}',
        moyenneClasse: '{{ $classProfile["moyenne_classe"] ?? "N/C" }}',
        principalTeacher: principalTeacher || 'N/C'
    };
    
    const profilItems = [
        ['Forte moyenne trim', classProfileData.forteMoyenne],
        ['Faible moyenne trim', classProfileData.faibleMoyenne],
        ['Moyenne de la classe', classProfileData.moyenneClasse],
        ['PROFESSEUR', 'EKOMESSE OLLO Giscard'],
        ['PRINCIPAL', classProfileData.principalTeacher]
    ];
    
    let profilY = yPos + 12;
    profilItems.forEach(item => {
        doc.text(item[0], margin + 8, profilY);
        doc.text(item[1], margin + sectionWidth - 8, profilY, { align: 'right' });
        profilY += 3;
    });
    
    // Bilan
    const bilanX = margin + sectionWidth + 10;
    doc.rect(bilanX, yPos, sectionWidth, 30, 'D');
    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('BILAN', bilanX + sectionWidth/2, yPos + 6, { align: 'center' });
    
    const bilanData = [
        ['1er Trimestre', '7.28', '8.89 44'],
        ['2ème Trimestre', '9.1 ▲', '8.61 24'],
        ['3ème Trimestre', '12.03 ▲', '10.79 18'],
        ['MOYENNE ANNUELLE:', '9.47 +0.09 (CC*)=10.00', '9.42 23']
    ];
    
    doc.autoTable({
        startY: yPos + 9,
        head: [['Moyenne', 'Apprenant', 'Classe Rang']],
        body: bilanData,
        theme: 'grid',
        styles: {
            fontSize: 5,
            cellPadding: 1
        },
        headStyles: {
            fillColor: [144, 238, 144],
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        margin: { left: bilanX + 2, right: margin + 5 },
        tableWidth: sectionWidth - 4
    });
    
    yPos += 40;
    
    // Décision du conseil
    doc.rect(margin + 5, yPos, pageWidth - 2 * margin - 10, 20, 'D');
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('DECISION DU CONSEIL DE CLASSE', pageWidth / 2, yPos + 6, { align: 'center' });
    
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Conduite : NC', margin + 20, yPos + 12);
    doc.text('Travail : Assez Bien/TH', pageWidth / 2, yPos + 12, { align: 'center' });
    doc.text('Fréquentation : A suivre', pageWidth - margin - 40, yPos + 12);
    
    // Badge Admis
    doc.setFillColor(0, 0, 0);
    doc.rect(pageWidth / 2 - 12, yPos + 14, 24, 5, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold');
    doc.text('Admis(e)', pageWidth / 2, yPos + 17, { align: 'center' });
    doc.setTextColor(0, 0, 0);
    
    yPos += 25;
    
    // Footer
    doc.line(margin + 5, yPos, pageWidth - margin - 5, yPos);
    yPos += 5;
    
    // Code-barre à gauche
    doc.rect(margin + 5, yPos + 3, 35, 8, 'D');
    doc.text('||||||||||||||||||||||||||||||||', margin + 22, yPos + 7, { align: 'center' });
    
    doc.setFontSize(8);
    doc.text(`*${String(studentData.id).padStart(16, '0')}*`, margin + 22, yPos + 15, { align: 'center' });
    
    // Signature à droite
    doc.setFontSize(8);
    doc.setFont('helvetica', 'bold');
    doc.text('Le Proviseur,', pageWidth - margin - 25, yPos + 3);
    doc.circle(pageWidth - margin - 25, yPos + 12, 6);
    // Zone blanche pour le cachet (pas de texte)
    
    // Conseil de classe au centre
    doc.setFontSize(6);
    doc.text('* Conseil de Classe', pageWidth / 2, yPos + 25, { align: 'center' });
    
         // Date
     doc.setFontSize(7);
     doc.text('05-01-2017', pageWidth / 2, yPos + 25, { align: 'center' });
     
     // Imprimer directement le PDF
     const pdfBlob = doc.output('blob');
     const pdfUrl = URL.createObjectURL(pdfBlob);
     
     // Créer une nouvelle fenêtre pour l'impression
     const printWindow = window.open(pdfUrl, '_blank');
     printWindow.onload = function() {
         printWindow.print();
         // Fermer la fenêtre après l'impression
         setTimeout(() => {
             printWindow.close();
             URL.revokeObjectURL(pdfUrl);
         }, 1000);
     };
 }

 function printHTML() {
     // Masquer les boutons d'action pour l'impression
     const actionButtons = document.querySelector('.action-buttons');
     if (actionButtons) {
         actionButtons.style.display = 'none';
     }
     
     // Imprimer la page HTML
     window.print();
     
     // Remettre les boutons après l'impression
     setTimeout(() => {
         if (actionButtons) {
             actionButtons.style.display = 'block';
         }
     }, 1000);
 }
</script>

  <!-- Modal pour éditer la note de conduite -->
 <div class="modal fade" id="conduiteModal" tabindex="-1" aria-labelledby="conduiteModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="conduiteModalLabel">Note de Conduite - {{ $student->first_name }} {{ $student->last_name }}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <form id="conduiteForm">
                     <div class="mb-3">
                         <label for="conduiteScore" class="form-label">Note de conduite (sur 20)</label>
                         <input type="number" class="form-control" id="conduiteScore" name="conduite_score" min="0" max="20" step="0.5" value="10">
                         <div class="form-text">Note sur 20 pour évaluer la conduite de l'élève</div>
                     </div>
                     <div class="mb-3">
                         <label for="conduiteComments" class="form-label">Commentaires</label>
                         <textarea class="form-control" id="conduiteComments" name="conduite_comments" rows="3" placeholder="Commentaires sur la conduite..."></textarea>
                     </div>
                     <div class="alert alert-info">
                         <strong>Info :</strong> La note de conduite a un coefficient de 0 et n'affecte pas la moyenne générale.
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                 <button type="button" class="btn btn-primary" onclick="saveConduite()">Enregistrer</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Données cachées -->
   @if(isset($student) && isset($class))
   <script id="student-data" type="application/json">{!! json_encode([
       'id' => $student->id ?? 0,
       'first_name' => $student->first_name ?? '',
       'last_name' => $student->last_name ?? '',
       'school_class' => $class ? ['name' => $class->name ?? ''] : null,
       'total_students' => $totalStudents ?? 0,
       'male_students' => $maleStudents ?? 0,
       'female_students' => $femaleStudents ?? 0
   ]) !!}</script>
   @else
   <script id="student-data" type="application/json">{}</script>
   @endif
   
   @if(isset($studentInfo))
   <script id="student-info" type="application/json">{!! json_encode($studentInfo) !!}</script>
   @else
   <script id="student-info" type="application/json">{}</script>
   @endif
   
   @if(isset($principalTeacherName))
   <script id="principal-teacher" type="application/json">{!! json_encode($principalTeacherName) !!}</script>
   @else
   <script id="principal-teacher" type="application/json">"N/C"</script>
   @endif

 <script>
 // Variables globales
 let studentData = null;
 let studentInfo = null;
 let principalTeacher = null;
 let conduiteNote = null;
 let conduiteComments = '';

 // Fonction pour parser JSON avec gestion d'erreur
 function safeJsonParse(elementId, defaultValue = null) {
     try {
         const element = document.getElementById(elementId);
         if (!element) {
             console.warn(`Élément ${elementId} non trouvé`);
             return defaultValue;
         }
         
         const content = element.textContent.trim();
         if (!content) {
             console.warn(`Contenu vide pour ${elementId}`);
             return defaultValue;
         }
         
         console.log(`Parsing JSON pour ${elementId}:`, content);
         return JSON.parse(content);
     } catch (error) {
         console.error(`Erreur lors du parsing JSON pour ${elementId}:`, error);
         console.error(`Contenu problématique:`, element ? element.textContent : 'Élément non trouvé');
         return defaultValue;
     }
 }

 // Initialisation des données au chargement de la page
 document.addEventListener('DOMContentLoaded', function() {
     console.log('DOMContentLoaded - Initialisation des données...');
     
     // Vérifier que les éléments existent
     const studentDataElement = document.getElementById('student-data');
     const studentInfoElement = document.getElementById('student-info');
     const principalTeacherElement = document.getElementById('principal-teacher');
     
     if (!studentDataElement) {
         console.error('Élément student-data non trouvé');
         return;
     }
     if (!studentInfoElement) {
         console.error('Élément student-info non trouvé');
         return;
     }
     if (!principalTeacherElement) {
         console.error('Élément principal-teacher non trouvé');
         return;
     }
     
     // Charger les données JSON de manière sécurisée
     studentData = safeJsonParse('student-data', {});
     studentInfo = safeJsonParse('student-info', {});
     principalTeacher = safeJsonParse('principal-teacher', 'N/C');
     
     console.log('Données chargées:', {
         studentData: studentData,
         studentInfo: studentInfo,
         principalTeacher: principalTeacher
     });
     
     // Charger la note de conduite existante
     if (studentData && studentData.id) {
         const existingNote = localStorage.getItem(`conduite_${studentData.id}`);
         if (existingNote) {
             try {
                 const data = JSON.parse(existingNote);
                 updateConduiteDisplay(data.score, data.comments);
             } catch (error) {
                 console.error('Erreur lors du chargement de la note de conduite:', error);
             }
         }
     }
 });

 // Fonction pour éditer la note de conduite
 function editConduite() {
     if (!studentData || !studentData.id) {
         alert('Erreur: Données étudiant non disponibles');
         return;
     }
     
     // Charger les données existantes si disponibles
     const existingNote = localStorage.getItem(`conduite_${studentData.id}`);
     if (existingNote) {
         try {
             const data = JSON.parse(existingNote);
             document.getElementById('conduiteScore').value = data.score || 10;
             document.getElementById('conduiteComments').value = data.comments || '';
         } catch (error) {
             console.error('Erreur lors du chargement des données existantes:', error);
             document.getElementById('conduiteScore').value = 10;
             document.getElementById('conduiteComments').value = '';
         }
     } else {
         document.getElementById('conduiteScore').value = 10;
         document.getElementById('conduiteComments').value = '';
     }
     
     // Afficher le modal
     const modal = new bootstrap.Modal(document.getElementById('conduiteModal'));
     modal.show();
 }

 // Fonction pour sauvegarder la note de conduite
 function saveConduite() {
     if (!studentData || !studentData.id) {
         alert('Erreur: Données étudiant non disponibles');
         return;
     }
     
     const score = parseFloat(document.getElementById('conduiteScore').value);
     const comments = document.getElementById('conduiteComments').value;
     
     if (isNaN(score) || score < 0 || score > 20) {
         alert('Veuillez entrer une note valide entre 0 et 20');
         return;
     }
     
     // Sauvegarder en localStorage (en attendant l'implémentation backend)
     const conduiteData = {
         score: score,
         comments: comments,
         date: new Date().toISOString()
     };
     localStorage.setItem(`conduite_${studentData.id}`, JSON.stringify(conduiteData));
     
     // Mettre à jour l'affichage
     updateConduiteDisplay(score, comments);
     
     // Fermer le modal
     const modal = bootstrap.Modal.getInstance(document.getElementById('conduiteModal'));
     modal.hide();
     
     // Afficher un message de succès
     alert('Note de conduite enregistrée avec succès !');
 }

 // Fonction pour mettre à jour l'affichage de la conduite
 function updateConduiteDisplay(score, comments) {
     const conduiteElement = document.getElementById('conduite-note');
     const conduiteButton = document.getElementById('conduite-button');
     if (!conduiteElement) return;
     
     if (score !== null && !isNaN(score)) {
         let appreciation = '';
         if (score >= 16) appreciation = ' (Excellent)';
         else if (score >= 14) appreciation = ' (Très bien)';
         else if (score >= 12) appreciation = ' (Bien)';
         else if (score >= 10) appreciation = ' (Assez bien)';
         else if (score >= 8) appreciation = ' (Passable)';
         else appreciation = ' (Insuffisant)';
         
         conduiteElement.textContent = `${score}/20${appreciation}`;
         conduiteElement.style.fontWeight = 'bold';
         conduiteElement.style.color = score >= 10 ? '#28a745' : '#dc3545';
         
         // Afficher le bouton Modifier
         if (conduiteButton) {
             conduiteButton.style.display = 'inline';
         }
     } else {
         conduiteElement.textContent = 'NC';
         conduiteElement.style.fontWeight = 'normal';
         conduiteElement.style.color = '#6c757d';
         
         // Masquer le bouton Modifier
         if (conduiteButton) {
             conduiteButton.style.display = 'none';
         }
     }
 }
 </script>
@endsection
