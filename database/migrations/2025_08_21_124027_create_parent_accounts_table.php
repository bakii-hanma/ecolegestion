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
        Schema::create('parent_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            
            // Informations de connexion
            $table->string('username')->unique(); // Nom d'utilisateur unique
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->unique();
            
            // Statut du compte
            $table->boolean('is_active')->default(true);
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            
            // Sécurité
            $table->string('verification_token')->nullable();
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // Préférences
            $table->json('notification_preferences')->nullable(); // Préférences de notifications
            $table->string('language', 5)->default('fr'); // Langue préférée
            $table->string('timezone')->default('Africa/Libreville');
            
            // Métadonnées
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['is_active', 'email_verified']);
            $table->index(['parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_accounts');
    }
};
