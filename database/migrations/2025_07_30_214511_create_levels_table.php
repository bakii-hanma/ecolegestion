<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // PS, MS, GS, CP, CE1, CE2, CM1, CM2, 6ème, 5ème, 4ème, 3ème, 2nde, 1ère, Terminal
            $table->string('code')->unique(); // PS, MS, GS, CP, CE1, CE2, CM1, CM2, 6EME, 5EME, 4EME, 3EME, 2NDE, 1ERE, TERMINAL
            $table->enum('cycle', ['preprimaire', 'primaire', 'college', 'lycee']); // preprimaire, primaire, college ou lycee
            $table->integer('order'); // Ordre d'affichage
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
