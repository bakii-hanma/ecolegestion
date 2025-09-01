<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du temps - {{ $class->name }} - {{ $academicYear->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .print-header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .print-header .info {
            font-size: 14px;
            color: #666;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 20px;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #000;
            padding: 8px 4px;
            vertical-align: top;
            text-align: center;
        }

        .schedule-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .schedule-table th:first-child {
            width: 100px;
        }

        .course-cell {
            background-color: #f8f9ff;
            border: 2px solid #007bff;
            border-radius: 4px;
            padding: 6px;
            margin: 2px;
            min-height: 50px;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .course-cell .subject {
            font-weight: bold;
            font-size: 10px;
            color: #000;
            margin-bottom: 3px;
        }

        .course-cell .teacher {
            font-size: 8px;
            color: #333;
            margin-bottom: 2px;
        }

        .course-cell .room {
            font-size: 8px;
            color: #666;
            font-style: italic;
        }

        .break-cell {
            background-color: #fff9e6;
            border: 2px solid #ffc107;
            border-radius: 4px;
            padding: 6px;
            margin: 2px;
            min-height: 30px;
            font-weight: bold;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .empty-cell {
            height: 40px;
            color: #ccc;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .print-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        @page {
            margin: 1cm;
            size: A4 landscape;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Bouton d'impression (caché à l'impression) -->
    <button onclick="window.print()" class="btn btn-primary print-button no-print">
        <i class="fas fa-print me-1"></i>
        Imprimer
    </button>

    <!-- En-tête de l'impression -->
    <div class="print-header">
        <h1>
            <i class="fas fa-calendar-alt me-2"></i>
            Emploi du temps - {{ $class->name }}
        </h1>
        <div class="info">
            <strong>Niveau :</strong> {{ $class->getSafeLevelName() }} | 
            <strong>Année académique :</strong> {{ $academicYear->name }} | 
            <strong>Imprimé le :</strong> {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    @if($schedulesByDay->count() > 0)
        <!-- Tableau de l'emploi du temps -->
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Horaires</th>
                    @foreach($days as $dayNumber => $dayName)
                        @if($schedulesByDay->has($dayNumber))
                            <th>{{ $dayName }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    // Récupérer tous les créneaux horaires uniques
                    $allSchedules = $schedulesByDay->flatten();
                    $timeSlots = $allSchedules->map(function($schedule) {
                        return $schedule->start_time->format('H:i') . '-' . $schedule->end_time->format('H:i');
                    })->unique()->sort();
                    
                    // Grouper par créneaux horaires
                    $schedulesByTimeSlot = [];
                    foreach($allSchedules as $schedule) {
                        $timeSlot = $schedule->start_time->format('H:i') . '-' . $schedule->end_time->format('H:i');
                        $schedulesByTimeSlot[$timeSlot][$schedule->day_of_week] = $schedule;
                    }
                @endphp

                @foreach($timeSlots as $timeSlot)
                    <tr>
                        <td style="background-color: #e9ecef; font-weight: bold;">
                            {{ str_replace('-', ' - ', $timeSlot) }}
                        </td>
                        @foreach($days as $dayNumber => $dayName)
                            @if($schedulesByDay->has($dayNumber))
                                <td style="padding: 4px;">
                                    @if(isset($schedulesByTimeSlot[$timeSlot][$dayNumber]))
                                        @php $schedule = $schedulesByTimeSlot[$timeSlot][$dayNumber] @endphp
                                        
                                        @if($schedule->type === 'course')
                                            <div class="course-cell">
                                                <div class="subject">
                                                    {{ $schedule->subject->name ?? 'Matière non définie' }}
                                                </div>
                                                <div class="teacher">
                                                    <i class="fas fa-user"></i>
                                                    {{ $schedule->teacher ? $schedule->teacher->first_name . ' ' . $schedule->teacher->last_name : 'Enseignant non défini' }}
                                                </div>
                                                @if($schedule->room)
                                                    <div class="room">
                                                        <i class="fas fa-door-open"></i>
                                                        {{ $schedule->room }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($schedule->type === 'break')
                                            <div class="break-cell">
                                                <i class="fas fa-coffee me-1"></i>
                                                {{ $schedule->title ?? 'Pause' }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="empty-cell">
                                            -
                                        </div>
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="print-footer">
            @php
                $totalSchedules = $allSchedules->count();
                $courseCount = $allSchedules->where('type', 'course')->count();
                $breakCount = $allSchedules->where('type', 'break')->count();
            @endphp
            <p>
                <strong>Statistiques :</strong>
                {{ $totalSchedules }} créneaux total | 
                {{ $courseCount }} heures de cours | 
                {{ $breakCount }} pauses
            </p>
            <p>
                Document généré automatiquement par le système de gestion scolaire
            </p>
        </div>

    @else
        <!-- Aucun emploi du temps -->
        <div class="text-center py-5">
            <h5>Aucun emploi du temps défini</h5>
            <p>Cette classe n'a pas encore d'emploi du temps pour l'année {{ $academicYear->name }}.</p>
        </div>
    @endif

    <script>
        // Auto-impression si demandée
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto') === '1') {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
