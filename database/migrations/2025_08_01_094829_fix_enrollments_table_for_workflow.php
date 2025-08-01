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
        // Vérifier et ajouter les colonnes manquantes
        if (!Schema::hasColumn('enrollments', 'applicant_first_name')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('applicant_first_name')->nullable()->after('student_id');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_last_name')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('applicant_last_name')->nullable()->after('applicant_first_name');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_date_of_birth')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->date('applicant_date_of_birth')->nullable()->after('applicant_last_name');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_gender')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->enum('applicant_gender', ['male', 'female'])->nullable()->after('applicant_date_of_birth');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_phone')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('applicant_phone')->nullable()->after('applicant_gender');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_email')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('applicant_email')->nullable()->after('applicant_phone');
            });
        }

        if (!Schema::hasColumn('enrollments', 'applicant_address')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->text('applicant_address')->nullable()->after('applicant_email');
            });
        }

        if (!Schema::hasColumn('enrollments', 'parent_first_name')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('parent_first_name')->nullable()->after('applicant_address');
            });
        }

        if (!Schema::hasColumn('enrollments', 'parent_last_name')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('parent_last_name')->nullable()->after('parent_first_name');
            });
        }

        if (!Schema::hasColumn('enrollments', 'parent_phone')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('parent_phone')->nullable()->after('parent_last_name');
            });
        }

        if (!Schema::hasColumn('enrollments', 'parent_email')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('parent_email')->nullable()->after('parent_phone');
            });
        }

        if (!Schema::hasColumn('enrollments', 'parent_relationship')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->enum('parent_relationship', ['father', 'mother', 'guardian', 'other'])->nullable()->after('parent_email');
            });
        }

        if (!Schema::hasColumn('enrollments', 'enrollment_status')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->enum('enrollment_status', ['pending', 'student_created', 'active', 'inactive', 'transferred', 'graduated'])
                      ->default('student_created')
                      ->after('status');
            });
        }

        if (!Schema::hasColumn('enrollments', 'is_new_enrollment')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->boolean('is_new_enrollment')->default(false)->after('enrollment_status');
            });
        }

        // Permettre student_id d'être nullable
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable()->change();
        });

        // Mettre à jour les données existantes
        DB::statement("UPDATE enrollments SET enrollment_status = 'student_created', is_new_enrollment = false WHERE student_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Remettre student_id comme obligatoire
            $table->unsignedBigInteger('student_id')->nullable(false)->change();
            
            // Supprimer les colonnes ajoutées (si elles existent)
            $columnsToRemove = [
                'applicant_first_name', 'applicant_last_name', 'applicant_date_of_birth', 'applicant_gender',
                'applicant_phone', 'applicant_email', 'applicant_address',
                'parent_first_name', 'parent_last_name', 'parent_phone', 'parent_email', 'parent_relationship',
                'enrollment_status', 'is_new_enrollment'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
