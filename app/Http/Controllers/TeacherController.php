<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Teacher::query();
        
        // Filtres
        if ($request->has('cycle') && $request->cycle) {
            $query->byCycle($request->cycle);
        }
        
        if ($request->has('teacher_type') && $request->teacher_type) {
            $query->byType($request->teacher_type);
        }
        
        $teachers = $query->with(['assignedClass'])->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistiques pour les cartes
        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('status', 'active')->count();
        $newThisMonth = Teacher::whereMonth('hire_date', now()->month)
                             ->whereYear('hire_date', now()->year)
                             ->count();
        $averageSalary = Teacher::where('status', 'active')->avg('salary');
        
        // Statistiques par cycle
        $teachersByCycle = [
            'preprimaire' => Teacher::byCycle('preprimaire')->count(),
            'primaire' => Teacher::byCycle('primaire')->count(),
            'college' => Teacher::byCycle('college')->count(),
            'lycee' => Teacher::byCycle('lycee')->count(),
        ];
        
        return view('teachers.index', compact(
            'teachers', 
            'totalTeachers', 
            'activeTeachers', 
            'newThisMonth', 
            'averageSalary',
            'teachersByCycle'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $levels = Level::active()->orderBy('order')->get();
        
        return view('teachers.create', compact('classes', 'subjects', 'levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers',
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'cycle' => 'required|in:preprimaire,primaire,college,lycee',
            'teacher_type' => 'required|in:general,specialized',
            'assigned_class_id' => 'nullable|exists:classes,id',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Validation spécifique selon le type d'enseignant
        if ($validated['teacher_type'] === 'general') {
            // Pour les généralistes, une classe est obligatoire
            if (empty($validated['assigned_class_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une classe doit être assignée pour un enseignant généraliste.'
                ], 422);
            }
        } else {
            // Pour les spécialisés, une spécialisation est obligatoire
            if (empty($validated['specialization'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une spécialisation est obligatoire pour un enseignant spécialisé.'
                ], 422);
            }
        }

        $teacher = Teacher::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enseignant ajouté avec succès!',
            'teacher' => $teacher
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['grades', 'subjects', 'assignedClass']);
        
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $levels = Level::active()->orderBy('order')->get();
        
        return view('teachers.edit', compact('teacher', 'classes', 'subjects', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers,employee_id,' . $teacher->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'cycle' => 'required|in:preprimaire,primaire,college,lycee',
            'teacher_type' => 'required|in:general,specialized',
            'assigned_class_id' => 'nullable|exists:classes,id',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Validation spécifique selon le type d'enseignant
        if ($validated['teacher_type'] === 'general') {
            if (empty($validated['assigned_class_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une classe doit être assignée pour un enseignant généraliste.'
                ], 422);
            }
        } else {
            if (empty($validated['specialization'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une spécialisation est obligatoire pour un enseignant spécialisé.'
                ], 422);
            }
        }

        $teacher->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enseignant modifié avec succès!',
            'teacher' => $teacher
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enseignant supprimé avec succès!'
        ]);
    }

    /**
     * Search teachers
     */
    public function search(Request $request)
    {
        $query = Teacher::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('cycle') && $request->cycle) {
            $query->byCycle($request->cycle);
        }

        if ($request->has('teacher_type') && $request->teacher_type) {
            $query->byType($request->teacher_type);
        }

        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $teachers = $query->get();

        return response()->json($teachers);
    }

    /**
     * Get classes by cycle for AJAX
     */
    public function getClassesByCycle(Request $request)
    {
        $cycle = $request->cycle;
        $classes = SchoolClass::where('is_active', true)
            ->whereHas('level', function($query) use ($cycle) {
                $query->where('cycle', $cycle);
            })
            ->get();
        
        return response()->json($classes);
    }
}
