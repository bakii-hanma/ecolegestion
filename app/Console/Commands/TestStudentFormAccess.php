<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestStudentFormAccess extends Command
{
    protected $signature = 'students:test-form-access';
    protected $description = 'Test access to student form and store method';

    public function handle()
    {
        $this->info('Test d\'accès au formulaire étudiant...');
        
        // Simuler une authentification
        $user = User::first();
        if (!$user) {
            $this->error('Aucun utilisateur trouvé. Créez un utilisateur admin d\'abord.');
            return;
        }
        
        Auth::login($user);
        $this->info("Connecté en tant que: {$user->name}");
        
        // Créer une requête de test
        $requestData = [
            'first_name' => 'Test',
            'last_name' => 'Console',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'place_of_birth' => 'Libreville',
            'address' => '123 Test Street',
            'enrollment_date' => now()->format('Y-m-d'),
            'status' => 'active',
            '_token' => csrf_token()
        ];
        
        $this->info('Données de test: ' . json_encode($requestData, JSON_PRETTY_PRINT));
        
        try {
            // Simuler la requête
            $request = Request::create('/students', 'POST', $requestData);
            $request->headers->set('X-CSRF-TOKEN', csrf_token());
            
            $controller = new StudentController();
            $response = $controller->store($request);
            
            $this->info('Réponse du contrôleur: ' . $response->getStatusCode());
            $this->info('Type de réponse: ' . get_class($response));
            
            if (method_exists($response, 'getTargetUrl')) {
                $this->info('Redirection vers: ' . $response->getTargetUrl());
            }
            
        } catch (\Exception $e) {
            $this->error('Erreur: ' . $e->getMessage());
            $this->error('Fichier: ' . $e->getFile() . ':' . $e->getLine());
            
            // Afficher les 5 premières lignes de la stack trace
            $traces = explode("\n", $e->getTraceAsString());
            foreach (array_slice($traces, 0, 5) as $trace) {
                $this->error($trace);
            }
        }
    }
}