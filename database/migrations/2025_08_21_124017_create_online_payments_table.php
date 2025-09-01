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
        Schema::create('online_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // ID unique de transaction
            $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            
            // Informations de paiement
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XAF');
            $table->enum('payment_type', ['enrollment', 're_enrollment', 'fees', 'other'])->default('enrollment');
            $table->enum('payment_method', ['mtn_mobile_money', 'moov_money', 'airtel_money', 'card'])->default('mtn_mobile_money');
            
            // Informations du payeur
            $table->string('payer_name');
            $table->string('payer_phone');
            $table->string('payer_email')->nullable();
            
            // Statut et suivi
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('gateway_response')->nullable(); // Réponse de la passerelle de paiement
            $table->string('gateway_transaction_id')->nullable(); // ID de transaction de la passerelle
            $table->timestamp('paid_at')->nullable();
            
            // Informations de sécurité
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['status', 'created_at']);
            $table->index(['payment_method', 'status']);
            $table->index(['enrollment_id', 'status']);
            $table->index(['parent_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_payments');
    }
};
