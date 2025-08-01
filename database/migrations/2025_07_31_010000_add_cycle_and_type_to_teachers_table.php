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
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('cycle', ['preprimaire', 'primaire', 'college', 'lycee'])->nullable()->after('specialization');
            $table->enum('teacher_type', ['general', 'specialized'])->default('general')->after('cycle');
            $table->foreignId('assigned_class_id')->nullable()->constrained('classes')->onDelete('set null')->after('teacher_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['assigned_class_id']);
            $table->dropColumn(['cycle', 'teacher_type', 'assigned_class_id']);
        });
    }
}; 