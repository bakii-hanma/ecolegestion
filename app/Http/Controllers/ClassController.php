<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * Afficher la liste des classes
     */
    public function index(Request $request)
    {
        $query = SchoolClass::with('level');
        
        // Filtrer par niveau si spécifié
        if ($request->has('level_id') && $request->level_id) {
            $query->where('level_id', $request->level_id);
    }

        // Filtrer par cycle (préprimaire/primaire/collège/lycée)
        if ($request->has('cycle') && $request->cycle) {
            $query->byCycle($request->cycle);
        }
        
        $classes = $query->orderBy('name')->paginate(10);
        $levels = Level::active()->orderBy('order')->get();

        return view('classes.index', compact('classes', 'levels'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $levels = Level::active()->orderBy('order')->get();
        return view('classes.create', compact('levels'));
    }

    /**
     * Enregistrer une nouvelle classe
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'level_id' => 'required|exists:levels,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SchoolClass::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès !');
    }

    /**
     * Afficher une classe spécifique
     */
    public function show(SchoolClass $class)
    {
        $class->load('level');
        return view('classes.show', compact('class'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(SchoolClass $class)
    {
        $levels = Level::active()->orderBy('order')->get();
        return view('classes.edit', compact('class', 'levels'));
    }

    /**
     * Mettre à jour une classe
     */
    public function update(Request $request, SchoolClass $class)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'level_id' => 'required|exists:levels,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Classe mise à jour avec succès !');
    }

    /**
     * Supprimer une classe
     */
    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès !');
    }

    /**
     * Obtenir les classes d'un niveau spécifique
     */
    public function byLevel(Level $level)
    {
        $classes = $level->classes()->with('level')->paginate(10);
        
        return view('classes.index', compact('classes', 'level'));
    }
}
