<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Level;
use App\Models\Student;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    /**
     * Affiche toutes les classes avec leurs niveaux
     */
    public function index()
    {
        // Récupérer toutes les classes avec leurs niveaux associés
        $classes = SchoolClass::with(['level' => function($query) {
                $query->select('id', 'name', 'code', 'cycle');
            }])
            ->orderBy('name')
            ->get();
            
        // Récupérer également tous les niveaux pour l'affichage par cycle
        $levels = Level::with(['classes' => function($query) {
                $query->select('id', 'name', 'level_id');
            }])
            ->active()
            ->orderBy('order')
            ->get();
            
        // Grouper les niveaux par cycle
        $levelsByCycle = [
            'preprimaire' => $levels->where('cycle', 'preprimaire'),
            'primaire' => $levels->where('cycle', 'primaire'),
            'college' => $levels->where('cycle', 'college'),
            'lycee' => $levels->where('cycle', 'lycee'),
        ];

        return view('bulletin.fich', compact('classes', 'levelsByCycle'));
    }

    /**
     * Affiche les notes d'un étudiant
     */
    public function studentGrades($studentId)
    {
        $student = Student::with(['grades.subject', 'grades.teacher'])
            ->findOrFail($studentId);
            
        return view('bulletin.student-grades', compact('student'));
    }

    /**
     * Affiche les détails d'une classe spécifique
     */
    public function show($id)
    {
        $classe = SchoolClass::with(['level', 'students'])
            ->findOrFail($id);
            
        return view('bulletin.classe-details', compact('classe'));
    }

    /**
     * Affiche les classes par niveau
     */
    public function byLevel($levelId)
    {
        $level = Level::with('classes')
            ->findOrFail($levelId);
            
        $classes = $level->classes()
            ->orderBy('name')
            ->get();
            
        return view('bulletin.by-level', compact('level', 'classes'));
    }

    /**
     * Affiche les classes par cycle
     */
    public function byCycle($cycle)
    {
        $levels = Level::with('classes')
            ->where('cycle', $cycle)
            ->active()
            ->orderBy('order')
            ->get();
            
        $cycleName = [
            'preprimaire' => 'Préprimaire',
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée'
        ][$cycle] ?? 'Cycle inconnu';
            
        return view('bulletin.by-cycle', compact('levels', 'cycle', 'cycleName'));
    }
}
