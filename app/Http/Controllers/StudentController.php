<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Level;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query();
        
        // Filtres
        if ($request->has('cycle') && $request->cycle) {
            $query->whereHas('enrollments.schoolClass.level', function($q) use ($request) {
                $q->where('cycle', $request->cycle);
            });
        }
        
        if ($request->has('level') && $request->level) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('level_id', $request->level);
            });
        }
        
        if ($request->has('class') && $request->class) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('id', $request->class);
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $students = $query->with([
                              'enrollments.schoolClass.level', 
                              'enrollments.schoolClass', 
                              'parents'
                          ])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        // Données pour les filtres
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->active()->get();
        
        // Statistiques
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $newThisMonth = Student::whereMonth('enrollment_date', now()->month)
                             ->whereYear('enrollment_date', now()->year)
                             ->count();
        
        // Statistiques par cycle
        $studentsByCycle = [
            'preprimaire' => Student::whereHas('enrollments.schoolClass.level', function($q) {
                $q->where('cycle', 'preprimaire');
            })->count(),
            'primaire' => Student::whereHas('enrollments.schoolClass.level', function($q) {
                $q->where('cycle', 'primaire');
            })->count(),
            'college' => Student::whereHas('enrollments.schoolClass.level', function($q) {
                $q->where('cycle', 'college');
            })->count(),
            'lycee' => Student::whereHas('enrollments.schoolClass.level', function($q) {
                $q->where('cycle', 'lycee');
            })->count(),
        ];
        
        return view('students.index', compact(
            'students', 
            'levels', 
            'classes',
            'totalStudents',
            'activeStudents', 
            'newThisMonth',
            'studentsByCycle'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::active()->orderBy('order')->get();
        $classes = SchoolClass::with('level')->where('is_active', true)->get();
        $academicYears = AcademicYear::where('status', 'active')->get();
        
        return view('students.create', compact('levels', 'classes', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'place_of_birth' => 'nullable|string|max:255',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string',
            'enrollment_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Élève ajouté avec succès!',
            'student' => $student
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['enrollments.schoolClass', 'parents', 'grades', 'attendances']);
        
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = SchoolClass::where('is_active', true)->get();
        $academicYears = AcademicYear::where('status', 'active')->get();
        
        return view('students.edit', compact('student', 'classes', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'place_of_birth' => 'nullable|string|max:255',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_conditions' => 'nullable|string',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Élève modifié avec succès!',
            'student' => $student
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Élève supprimé avec succès!'
        ]);
    }

    /**
     * Search students
     */
    public function search(Request $request)
    {
        $query = Student::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->has('class') && $request->class) {
            $query->whereHas('enrollments.schoolClass', function($q) use ($request) {
                $q->where('name', $request->class);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->with(['enrollments.schoolClass', 'parents'])->get();

        return response()->json($students);
    }

    /**
     * Get classes by level for AJAX
     */
    public function getClassesByLevel(Request $request)
    {
        $levelId = $request->level_id;
        $classes = SchoolClass::where('is_active', true)
            ->where('level_id', $levelId)
            ->get();
        
        return response()->json($classes);
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
            ->with('level')
            ->get();
        
        return response()->json($classes);
    }
}
