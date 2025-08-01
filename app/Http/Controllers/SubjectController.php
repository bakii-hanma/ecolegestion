<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Afficher la liste des matières
     */
    public function index(Request $request)
    {
        $query = Subject::with('level');
        
        // Filtrer par niveau si spécifié
        if ($request->has('level_id') && $request->level_id) {
            $query->where('level_id', $request->level_id);
        }

        // Filtrer par cycle (collège/lycée)
        if ($request->has('cycle') && $request->cycle) {
            $query->byCycle($request->cycle);
        }
        
        $subjects = $query->orderBy('name')->paginate(15);
        $levels = Level::active()->orderBy('order')->get();

        return view('subjects.index', compact('subjects', 'levels'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $levels = Level::active()->orderBy('order')->get();
        return view('subjects.create', compact('levels'));
    }

    /**
     * Enregistrer une nouvelle matière
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'level_id' => 'required|exists:levels,id',
            'is_active' => 'boolean'
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Matière créée avec succès!',
            'subject' => $subject
        ]);
    }

    /**
     * Afficher une matière spécifique
     */
    public function show(Subject $subject)
    {
        $subject->load('level');
        return view('subjects.show', compact('subject'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Subject $subject)
    {
        $levels = Level::active()->orderBy('order')->get();
        return view('subjects.edit', compact('subject', 'levels'));
    }

    /**
     * Mettre à jour une matière
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'level_id' => 'required|exists:levels,id',
            'is_active' => 'boolean'
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Matière mise à jour avec succès!',
            'subject' => $subject
        ]);
    }

    /**
     * Supprimer une matière
     */
    public function destroy(Subject $subject)
    {
        // Vérifier s'il y a des notes associées
        if ($subject->grades()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette matière car elle a des notes associées.'
            ], 422);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Matière supprimée avec succès!'
        ]);
    }

    /**
     * Obtenir les matières d'un niveau spécifique
     */
    public function byLevel(Level $level)
    {
        $subjects = $level->subjects()->paginate(15);
        
        return view('subjects.index', compact('subjects', 'level'));
    }

    /**
     * Rechercher des matières
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $subjects = Subject::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->with('level')
            ->paginate(15);

        return view('subjects.index', compact('subjects'));
    }
}
