<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Level;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
        
        // Tri des résultats
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Valider les options de tri
        $allowedSorts = ['name', 'created_at', 'capacity'];
        $allowedDirections = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'desc';
        }
        
        $classes = $query->orderBy($sortBy, $sortDirection)->paginate(10);
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
        // Debug : Log des données reçues
        Log::info('=== CREATION CLASSE DEBUG ===');
        Log::info('Données reçues', ['data' => $request->all()]);
        Log::info('Method', ['method' => $request->method()]);
        Log::info('URL', ['url' => $request->url()]);
        
        // Validation simplifiée pour debug - sans validation des enseignants
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id'
        ]);

        if ($validator->fails()) {
            Log::error('ERREURS DE VALIDATION');
            Log::error('Erreurs détaillées', ['errors' => $validator->errors()->toArray()]);
            Log::error('Règles de validation', [
                'rules' => [
                    'name' => 'required|string|max:255',
                    'level_id' => 'required|exists:levels,id',
                    'teachers' => 'array'
                ]
            ]);
            
            // Vérification spécifique des champs requis
            Log::error('Valeur name', ['name' => $request->get('name')]);
            Log::error('Valeur level_id', ['level_id' => $request->get('level_id')]);
            Log::error('Valeur teachers', ['teachers' => $request->get('teachers')]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Erreur de validation : ' . $validator->errors()->first());
        }

        try {
            // Préparer les données pour la création
            $data = $request->only(['name', 'description', 'capacity', 'level_id', 'series']);
            $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;
            
            Log::info('Données préparées pour création', ['data' => $data]);

            $class = SchoolClass::create($data);
            
            // Associer les professeurs à la classe
            if ($request->has('teachers') && is_array($request->teachers)) {
                $teachers = $request->teachers;
                $teacherRoles = [];
                
                foreach ($teachers as $teacherId) {
                    if ($teacherId) {
                        $teacher = \App\Models\Teacher::find($teacherId);
                        if ($teacher) {
                            // Déterminer le rôle du professeur
                            $role = 'teacher';
                            if ($teacher->teacher_type === 'general') {
                                $role = 'principal';
                                // Assigner aussi la classe pour les professeurs généralistes
                                $teacher->update(['assigned_class_id' => $class->id]);
                            }
                            
                            // Ajouter à la liste des rôles
                            $teacherRoles[$teacherId] = ['role' => $role];
                        }
                    }
                }
                
                // Associer tous les professeurs à la classe via la table pivot
                if (!empty($teacherRoles)) {
                    $class->allTeachers()->sync($teacherRoles);
                }
            }
            
            Log::info('Classe créée avec succès:', ['id' => $class->id, 'name' => $class->name]);

            return redirect()->route('classes.index')
                ->with('success', 'Classe créée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de classe:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
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
        try {
            // Vérifier s'il y a des élèves inscrits dans cette classe
            $studentsCount = \App\Models\Student::whereHas('enrollments', function($query) use ($class) {
                $query->where('class_id', $class->id);
            })->count();
            
            if ($studentsCount > 0) {
                return redirect()->back()
                    ->with('error', "Impossible de supprimer la classe {$class->name}. Elle contient encore {$studentsCount} élève(s) inscrit(s). Veuillez d'abord transférer ou désinscrire tous les élèves.");
            }
            
            // Vérifier s'il y a des notes ou présences liées à cette classe
            $gradesCount = \App\Models\Grade::whereHas('enrollment', function($query) use ($class) {
                $query->where('class_id', $class->id);
            })->count();
            
            $attendancesCount = \App\Models\Attendance::whereHas('enrollment', function($query) use ($class) {
                $query->where('class_id', $class->id);
            })->count();
            
            if ($gradesCount > 0 || $attendancesCount > 0) {
                Log::warning("Suppression de classe avec données: {$class->name} (Notes: {$gradesCount}, Présences: {$attendancesCount})");
            }
            
            // Supprimer d'abord les enregistrements liés (dans l'ordre inverse des dépendances)
            // 1. Présences
            \App\Models\Attendance::whereHas('enrollment', function($query) use ($class) {
                $query->where('class_id', $class->id);
            })->delete();
            
            // 2. Notes
            \App\Models\Grade::whereHas('enrollment', function($query) use ($class) {
                $query->where('class_id', $class->id);
            })->delete();
            
            // 3. Emplois du temps
            \App\Models\Schedule::where('class_id', $class->id)->delete();
            
            // 4. Inscriptions historiques (si elles existent encore)
            \App\Models\Enrollment::where('class_id', $class->id)->delete();
            
            // 5. Enfin, supprimer la classe
            $className = $class->name;
            $class->delete();
            
            Log::info("Classe supprimée avec succès: {$className}");
            
            return redirect()->route('classes.index')
                ->with('success', "Classe '{$className}' supprimée avec succès !");
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de classe:', [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les classes d'un niveau spécifique
     */
    public function byLevel(Level $level)
    {
        $classes = $level->classes()->with('level')->paginate(10);
        
        return view('classes.index', compact('classes', 'level'));
    }

    /**
     * Afficher les élèves d'une classe spécifique
     */
    public function students(SchoolClass $class)
    {
        $class->load('level');
        
        // Récupérer les élèves inscrits dans cette classe pour l'année en cours
        $students = \App\Models\Student::whereHas('enrollments', function($query) use ($class) {
            $query->where('class_id', $class->id)
                  ->where('status', 'active');
        })->with(['enrollments' => function($query) use ($class) {
            $query->where('class_id', $class->id)
                  ->where('status', 'active');
        }])->orderBy('last_name')->get();
        
        return view('classes.students', compact('class', 'students'));
    }

    /**
     * API: Obtenir les professeurs par niveau
     */
    public function getTeachersByLevel($levelId)
    {
        try {
            $level = Level::findOrFail($levelId);
            
            // Récupérer les professeurs selon le cycle du niveau
            $teachers = \App\Models\Teacher::where('cycle', $level->cycle)
                ->where('status', 'active')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'teacher_type', 'specialization']);
            
            return response()->json([
                'success' => true,
                'teachers' => $teachers
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des professeurs par niveau:', [
                'level_id' => $levelId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des professeurs'
            ], 500);
        }
    }

    /**
     * API: Obtenir les matières par niveau
     */
    public function getSubjectsByLevel($levelId)
    {
        try {
            $level = Level::findOrFail($levelId);
            
            // Récupérer les matières selon le niveau
            $subjects = \App\Models\Subject::where('level_id', $levelId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code']);
            
            return response()->json([
                'success' => true,
                'subjects' => $subjects
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des matières par niveau:', [
                'level_id' => $levelId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des matières'
            ], 500);
        }
    }

    /**
     * Afficher la gestion des professeurs d'une classe
     */
    public function teachers(SchoolClass $class)
    {
        $class->load(['level', 'allTeachers']);
        
        // Récupérer tous les professeurs disponibles pour ce niveau
        // Déterminer le cycle en fonction du type de $class->level
        if (is_object($class->level)) {
            // Si level est un objet Level (relation chargée)
            $cycle = $class->level->cycle;
        } else {
            // Si level est une chaîne (champ direct)
            $cycle = $class->level;
        }
        
        $availableTeachers = \App\Models\Teacher::where('cycle', $cycle)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        
        // Récupérer les professeurs actuellement assignés
        $assignedTeachers = $class->allTeachers()->withPivot('role')->get();
        
        return view('classes.teachers', compact('class', 'availableTeachers', 'assignedTeachers'));
    }

    /**
     * API: Récupérer les classes existantes pour un niveau et suggérer le prochain nom
     */
    public function getExistingClassesForLevel(Request $request, $levelId)
    {
        try {
            $level = Level::findOrFail($levelId);
            $series = $request->get('series', null);
            
            // Récupérer les classes existantes pour ce niveau
            $existingClasses = SchoolClass::where('level_id', $levelId)
                ->when($series, function($query) use ($series) {
                    $query->where('series', $series);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'series']);
            
            // Analyser les noms existants pour déterminer le type d'incrémentation
            $incrementType = null;
            $nextIncrement = null;
            $usedIncrements = [];
            
            if ($existingClasses->count() > 0) {
                // Extraire les dernières parties des noms (incrémentation)
                foreach ($existingClasses as $class) {
                    $name = $class->name;
                    // Pattern pour extraire l'incrémentation à la fin
                    if (preg_match('/(\d+)$/', $name, $matches)) {
                        $incrementType = 'number';
                        $usedIncrements[] = (int) $matches[1];
                    } elseif (preg_match('/([A-Z])$/', $name, $matches)) {
                        $incrementType = 'letter';
                        $usedIncrements[] = $matches[1];
                    }
                }
                
                // Déterminer le prochain incrément
                if ($incrementType === 'number') {
                    $nextIncrement = max($usedIncrements) + 1;
                } elseif ($incrementType === 'letter') {
                    $lastLetter = max($usedIncrements);
                    $nextIncrement = chr(ord($lastLetter) + 1);
                }
            }
            
            // Générer le nom suggéré
            $suggestedName = $level->name;
            if ($level->cycle === 'lycee' && $series) {
                $suggestedName .= " {$series}";
            }
            if ($nextIncrement) {
                $suggestedName .= " {$nextIncrement}";
            }
            
            return response()->json([
                'success' => true,
                'level' => $level,
                'existing_classes' => $existingClasses,
                'increment_type' => $incrementType,
                'next_increment' => $nextIncrement,
                'suggested_name' => $suggestedName,
                'is_first_class' => $existingClasses->count() === 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des classes existantes:', [
                'level_id' => $levelId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des classes existantes'
            ], 500);
        }
    }

    /**
     * API: Récupérer les enseignants disponibles pour un niveau donné
     */
    public function getTeachersForLevel($levelId)
    {
        try {
            $level = Level::findOrFail($levelId);
            $cycle = $level->cycle;
            
            // Récupérer les enseignants selon le cycle et le type
            $teachersQuery = Teacher::where('cycle', $cycle)
                ->where('status', 'active');
            
            // Pour le primaire, ne prendre que les enseignants généralistes
            if ($cycle === 'primaire') {
                $teachersQuery->where('teacher_type', 'general');
            }
            
            $teachers = $teachersQuery->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'teacher_type', 'specialization', 'cycle']);
            
            return response()->json([
                'success' => true,
                'level' => $level,
                'cycle' => $cycle,
                'teachers' => $teachers,
                'is_primary' => $cycle === 'primaire',
                'max_teachers' => $cycle === 'primaire' ? 1 : null // Limiter à 1 pour le primaire
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des enseignants par niveau:', [
                'level_id' => $levelId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des enseignants'
            ], 500);
        }
    }

    /**
     * Mettre à jour les professeurs d'une classe
     */
    public function updateTeachers(Request $request, SchoolClass $class)
    {
        $validator = Validator::make($request->all(), [
            'teachers' => 'array',
            'teachers.*' => 'exists:teachers,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Erreur de validation');
        }

        try {
            $teachers = $request->input('teachers', []);
            $teacherRoles = [];
            
            // Récupérer les professeurs actuellement assignés pour préserver leurs rôles
            $currentTeachers = $class->allTeachers()->withPivot('role')->get();
            $currentRoles = [];
            foreach ($currentTeachers as $currentTeacher) {
                $currentRoles[$currentTeacher->id] = $currentTeacher->pivot->role;
            }
            
            // Vérifier s'il y a déjà un professeur principal
            $hasPrincipal = false;
            foreach ($currentRoles as $role) {
                if ($role === 'principal') {
                    $hasPrincipal = true;
                    break;
                }
            }
            
            foreach ($teachers as $teacherId) {
                if ($teacherId) {
                    $teacher = \App\Models\Teacher::find($teacherId);
                    if ($teacher) {
                        // Préserver le rôle existant ou définir un rôle par défaut
                        $role = $currentRoles[$teacherId] ?? 'teacher';
                        
                        // Si c'est un nouveau professeur et qu'il n'y a pas encore de principal
                        if (!isset($currentRoles[$teacherId])) {
                            if (!$hasPrincipal && $teacher->teacher_type === 'general') {
                                $role = 'principal';
                                $hasPrincipal = true; // Marquer qu'on a maintenant un principal
                            }
                        }
                        
                        $teacherRoles[$teacherId] = ['role' => $role];
                    }
                }
            }
            
            // Mettre à jour les associations
            $class->allTeachers()->sync($teacherRoles);
            
            return redirect()->back()
                ->with('success', 'Professeurs mis à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des professeurs:', [
                'class_id' => $class->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des professeurs');
        }
    }

    /**
     * Définir un professeur principal pour une classe
     */
    public function setPrincipalTeacher(Request $request, SchoolClass $class, \App\Models\Teacher $teacher)
    {
        try {
            // Récupérer tous les professeurs assignés à cette classe
            $assignedTeachers = $class->allTeachers()->withPivot('role')->get();
            
            // Préparer les données pour la synchronisation
            $teacherRoles = [];
            
            foreach ($assignedTeachers as $assignedTeacher) {
                if ($assignedTeacher->id == $teacher->id) {
                    // Définir ce professeur comme principal
                    $teacherRoles[$assignedTeacher->id] = ['role' => 'principal'];
                } else {
                    // Retirer le statut principal des autres professeurs
                    $teacherRoles[$assignedTeacher->id] = ['role' => 'teacher'];
                }
            }
            
            // Mettre à jour les associations
            $class->allTeachers()->sync($teacherRoles);
            
            return redirect()->back()
                ->with('success', "{$teacher->first_name} {$teacher->last_name} est maintenant le professeur principal de cette classe !");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la définition du professeur principal:', [
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la définition du professeur principal');
        }
    }

    /**
     * Retirer le statut de professeur principal d'un enseignant
     */
    public function removePrincipalTeacher(Request $request, SchoolClass $class, \App\Models\Teacher $teacher)
    {
        try {
            // Vérifier que le professeur est bien le principal actuel
            $currentPrincipal = $class->allTeachers()
                ->where('pivot.role', 'principal')
                ->first();
            
            if (!$currentPrincipal || $currentPrincipal->id !== $teacher->id) {
                return redirect()->back()
                    ->with('error', 'Ce professeur n\'est pas le professeur principal actuel');
            }
            
            // Récupérer tous les professeurs assignés à cette classe
            $assignedTeachers = $class->allTeachers()->withPivot('role')->get();
            
            // Préparer les données pour la synchronisation
            $teacherRoles = [];
            
            foreach ($assignedTeachers as $assignedTeacher) {
                if ($assignedTeacher->id == $teacher->id) {
                    // Retirer le statut principal de ce professeur
                    $teacherRoles[$assignedTeacher->id] = ['role' => 'teacher'];
                } else {
                    // Préserver le rôle des autres professeurs
                    $teacherRoles[$assignedTeacher->id] = ['role' => $assignedTeacher->pivot->role];
                }
            }
            
            // Mettre à jour les associations
            $class->allTeachers()->sync($teacherRoles);
            
            return redirect()->back()
                ->with('success', "Le statut de professeur principal a été retiré à {$teacher->first_name} {$teacher->last_name}");
        } catch (\Exception $e) {
            Log::error('Erreur lors du retrait du statut de professeur principal:', [
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors du retrait du statut de professeur principal');
        }
    }
}
