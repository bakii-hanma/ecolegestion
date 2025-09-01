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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la passerelle (MTN Mobile Money, Moov Money, etc.)
            $table->string('code')->unique(); // Code unique (mtn_mobile_money, moov_money, etc.)
            $table->text('description')->nullable();
            
            // Configuration
            $table->json('config')->nullable(); // Configuration spécifique (clés API, URLs, etc.)
            $table->string('logo_url')->nullable(); // URL du logo
            $table->string('color')->nullable(); // Couleur de la passerelle
            
            // Statut
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(true); // Mode test/production
            $table->decimal('transaction_fee', 5, 2)->default(0.00); // Frais de transaction en %
            $table->decimal('fixed_fee', 8, 2)->default(0.00); // Frais fixes
            
            // Limites
            $table->decimal('min_amount', 10, 2)->default(100.00); // Montant minimum
            $table->decimal('max_amount', 10, 2)->default(1000000.00); // Montant maximum
            
            // Métadonnées
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['is_active', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
