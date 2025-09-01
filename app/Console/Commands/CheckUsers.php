<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check existing users and their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        $this->info('=== UTILISATEURS SYSTÈME ===');
        
        foreach ($users as $user) {
            $status = $user->is_active ? 'Actif' : 'Inactif';
            $matricule = $user->matricule ?? 'Non défini';
            $this->line("Matricule: {$matricule} | Email: {$user->email} | Rôle: {$user->role} | Statut: {$status}");
        }
        
        $this->info("\nTotal: " . $users->count() . " utilisateurs");
        
        return 0;
    }
}