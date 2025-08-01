<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Level;
use App\Models\SchoolClass;

class CheckLevelsAndClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:levels-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les niveaux et classes créés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VÉRIFICATION DES NIVEAUX ET CLASSES ===');
        $this->newLine();

        // Vérifier les niveaux
        $this->info('📚 NIVEAUX DISPONIBLES:');
        $levels = Level::where('is_active', true)->orderBy('order')->get();
        
        $levelsByCycle = $levels->groupBy('cycle');
        
        foreach ($levelsByCycle as $cycle => $cyclelevels) {
            $this->warn("🔸 " . strtoupper($cycle) . ":");
            foreach ($cyclelevels as $level) {
                $this->line("   - {$level->name} ({$level->code})");
            }
            $this->newLine();
        }

        // Vérifier les classes
        $this->info('🏫 CLASSES CRÉÉES:');
        $classes = SchoolClass::with('level')->where('is_active', true)->orderBy('level_id')->get();
        
        $classesByLevel = $classes->groupBy('level.name');
        
        foreach ($classesByLevel as $levelName => $levelClasses) {
            $this->warn("📋 {$levelName}:");
            foreach ($levelClasses as $class) {
                $capacity = $class->capacity;
                $this->line("   - {$class->name} (Capacité: {$capacity} élèves)");
            }
            $this->newLine();
        }

        // Statistiques
        $this->info('📊 STATISTIQUES:');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Niveaux actifs', $levels->count()],
                ['Classes créées', $classes->count()],
                ['Niveaux pré-primaire', $levels->where('cycle', 'preprimaire')->count()],
                ['Niveaux primaire', $levels->where('cycle', 'primaire')->count()],
                ['Niveaux collège', $levels->where('cycle', 'college')->count()],
                ['Niveaux lycée', $levels->where('cycle', 'lycee')->count()],
                ['Classes pré-primaire', $classes->filter(fn($c) => $c->level && $c->level->cycle === 'preprimaire')->count()],
                ['Classes primaire', $classes->filter(fn($c) => $c->level && $c->level->cycle === 'primaire')->count()],
                ['Classes collège', $classes->filter(fn($c) => $c->level && $c->level->cycle === 'college')->count()],
            ]
        );

        // Vérifier les problèmes potentiels
        $classesWithoutLevel = SchoolClass::whereNull('level_id')->count();
        if ($classesWithoutLevel > 0) {
            $this->error("⚠️  {$classesWithoutLevel} classes sans niveau assigné détectées!");
        }

        $inactiveLevels = Level::where('is_active', false)->count();
        if ($inactiveLevels > 0) {
            $this->comment("ℹ️  {$inactiveLevels} niveaux inactifs (probablement lycée)");
        }

        $this->newLine();
        $this->info('✅ Vérification terminée!');
        
        return Command::SUCCESS;
    }
}
