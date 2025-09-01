<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ParentModel::query();
        
        // Filtres
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('relationship') && $request->relationship) {
            $query->where('relationship', $request->relationship);
        }
        
        if ($request->has('is_primary_contact') && $request->is_primary_contact !== '') {
            $query->where('is_primary_contact', (bool)$request->is_primary_contact);
        }
        
        if ($request->has('can_pickup') && $request->can_pickup !== '') {
            $query->where('can_pickup', (bool)$request->can_pickup);
        }
        
        $parents = $query->with('students')->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques
        $totalParents = ParentModel::count();
        $primaryContacts = ParentModel::where('is_primary_contact', true)->count();
        $canPickup = ParentModel::where('can_pickup', true)->count();
        
        // Statistiques par relation
        $parentsByRelation = [
            'father' => ParentModel::where('relationship', 'father')->count(),
            'mother' => ParentModel::where('relationship', 'mother')->count(),
            'guardian' => ParentModel::where('relationship', 'guardian')->count(),
            'other' => ParentModel::where('relationship', 'other')->count(),
        ];
        
        return view('parents.index', compact(
            'parents', 
            'totalParents', 
            'primaryContacts', 
            'canPickup',
            'parentsByRelation'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $students = Student::active()->orderBy('last_name')->get();
        
        // Si on vient de l'inscription ou de la création d'élève
        $preselectedStudent = null;
        $fromContext = $request->get('from'); // 'enrollment' ou 'student'
        
        if ($request->has('student_id')) {
            $studentId = $request->get('student_id');
            
            if ($fromContext === 'enrollment') {
                // L'ID vient d'une inscription, récupérer l'élève via l'inscription
                $enrollment = \App\Models\Enrollment::find($studentId);
                if ($enrollment && $enrollment->student) {
                    $preselectedStudent = $enrollment->student;
                }
            } else {
                // L'ID est directement celui de l'élève
                $preselectedStudent = Student::find($studentId);
            }
        }
        
        return view('parents.create', compact('students', 'preselectedStudent', 'fromContext'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:parents|unique:users',
            'phone' => 'required|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'workplace' => 'nullable|string|max:255',
            'relationship' => 'required|in:father,mother,guardian,other',
            'is_primary_contact' => 'boolean',
            'can_pickup' => 'boolean',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $parent = ParentModel::create($validated);
        
        $successMessage = 'Parent ajouté avec succès!';
        
        // Créer un compte utilisateur pour le parent seulement si un email est fourni
        if (!empty($validated['email'])) {
            $generatedPassword = $validated['phone'] . '1234';
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($generatedPassword),
                'role' => 'parent',
                'matricule' => 'PAR' . str_pad($parent->id, 6, '0', STR_PAD_LEFT)
            ]);
            
            // Lier le parent à l'utilisateur
            $parent->update(['user_id' => $user->id]);
            
            $successMessage = 'Parent ajouté avec succès! Mot de passe généré: ' . $generatedPassword;
        }
        
        // Associer les étudiants
        $parent->students()->attach($validated['student_ids']);

        return redirect()->route('parents.index')->with('success', $successMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentModel $parent)
    {
        $parent->load('students');
        return view('parents.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentModel $parent)
    {
        $students = Student::active()->orderBy('last_name')->get();
        $parent->load('students');
        return view('parents.edit', compact('parent', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParentModel $parent)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:parents,email,' . $parent->id . '|unique:users,email,' . ($parent->user_id ?? 'NULL'),
            'phone' => 'required|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'workplace' => 'nullable|string|max:255',
            'relationship' => 'required|in:father,mother,guardian,other',
            'is_primary_contact' => 'boolean',
            'can_pickup' => 'boolean',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $parent->update($validated);
        
        // Synchroniser les étudiants
        $parent->students()->sync($validated['student_ids']);

        return redirect()->route('parents.index')->with('success', 'Parent modifié avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentModel $parent)
    {
        $parent->delete();

        return response()->json([
            'success' => true,
            'message' => 'Parent supprimé avec succès!'
        ]);
    }

    /**
     * Search parents
     */
    public function search(Request $request)
    {
        $query = ParentModel::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('relationship') && $request->relationship) {
            $query->where('relationship', $request->relationship);
        }

        $parents = $query->with('students')->get();

        return response()->json($parents);
    }
}
