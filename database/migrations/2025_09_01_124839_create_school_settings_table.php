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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('Lycée XXXXX');
            $table->string('school_address')->nullable();
            $table->string('school_phone')->default('06037499');
            $table->string('school_email')->nullable();
            $table->string('school_website')->nullable();
            $table->string('school_bp')->default('BP: 6');
            $table->string('school_logo')->nullable();
            $table->string('school_seal')->nullable();
            $table->text('school_motto')->nullable();
            $table->text('school_description')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_title')->default('Le Proviseur');
            $table->string('academic_year')->default('2024-2025');
            $table->string('school_type')->default('Lycée');
            $table->string('school_level')->default('Secondaire');
            $table->string('country')->default('Gabon');
            $table->string('city')->default('Libreville');
            $table->string('timezone')->default('Africa/Libreville');
            $table->string('currency')->default('FCFA');
            $table->string('language')->default('fr');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
