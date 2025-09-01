<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter 'parent' à l'enum role existant
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'teacher', 'secretary', 'parent') DEFAULT 'teacher'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'parent' de l'enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'teacher', 'secretary') DEFAULT 'teacher'");
    }
};
