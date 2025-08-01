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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Frais de scolarité", "Frais d'inscription"
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('fee_type', ['tuition', 'registration', 'uniform', 'transport', 'meal', 'other'])->default('tuition');
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly', 'one_time'])->default('monthly');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade'); // Null si applicable à toutes les classes
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('due_date')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
