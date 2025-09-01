<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    /**
     * Afficher la liste des matières
     */
    public function index(Request $request)
    {
        $query = Subject::query();
        
        // Filtrer par cycle si spécifié
        if ($request->has('cycle') && $request->cycle) {
            $query->where('cycle', $request->cycle);
        }

        // Filtrer par série si spécifié (pour le lycée)
        if ($request->has('series') && $request->series) {
            $query->whereJsonContains('series', $request->series);
        }
        
        $subjects = $query->orderBy('cycle')->orderBy('name')->paginate(15);

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Enregistrer une nouvelle matière
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'cycle' => 'required|in:primaire,college,lycee',
            'is_active' => 'boolean'
        ];

        // Validation conditionnelle pour les séries du lycée
        if ($request->cycle === 'lycee') {
            $rules['series'] = 'required|array|min:1';
            $rules['series.*'] = 'in:S,A1,A2,B,C,D,E,LE';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Pour les cycles non-lycée, pas de séries
        if ($request->cycle !== 'lycee') {
            $validated['series'] = null;
        }

        try {
            $subject = Subject::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Matière créée avec succès!',
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la matière: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la matière: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une matière spécifique
     */
    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Mettre à jour une matière
     */
    public function update(Request $request, Subject $subject)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'cycle' => 'required|in:primaire,college,lycee',
            'is_active' => 'boolean'
        ];

        // Validation conditionnelle pour les séries du lycée
        if ($request->cycle === 'lycee') {
            $rules['series'] = 'required|array|min:1';
            $rules['series.*'] = 'in:S,A1,A2,B,C,D,E,LE';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Pour les cycles non-lycée, pas de séries
        if ($request->cycle !== 'lycee') {
            $validated['series'] = null;
        }

        try {
            $subject->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Matière mise à jour avec succès!',
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de la matière: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la matière: ' . $e->getMessage()
            ], 500);
        }
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
     * Obtenir les matières d'un cycle spécifique
     */
    public function byCycle(Request $request)
    {
        $cycle = $request->get('cycle');
        $subjects = Subject::where('cycle', $cycle)->paginate(15);
        
        return view('subjects.index', compact('subjects'));
    }

    /**
     * Rechercher des matières
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $subjects = Subject::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('cycle', 'like', "%{$query}%")
            ->paginate(15);

        return view('subjects.index', compact('subjects'));
    }
}
