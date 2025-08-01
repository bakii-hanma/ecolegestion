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
        Schema::table('enrollments', function (Blueprint $table) {
            // Informations de frais et paiement
            $table->decimal('total_fees', 10, 2)->default(0)->after('notes')->comment('Frais total d\'inscription');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_fees')->comment('Montant payé');
            $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid')->comment('Reste à percevoir');
            
            // Informations de paiement
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'mobile_money', 'other'])->nullable()->after('balance_due')->comment('Méthode de paiement');
            $table->string('payment_reference')->nullable()->after('payment_method')->comment('Référence du paiement');
            $table->text('payment_notes')->nullable()->after('payment_reference')->comment('Notes sur le paiement');
            
            // Statut de paiement
            $table->enum('payment_status', ['pending', 'partial', 'completed', 'overdue'])->default('pending')->after('payment_notes')->comment('Statut du paiement');
            $table->date('payment_due_date')->nullable()->after('payment_status')->comment('Date limite de paiement');
            
            // Numéro de reçu
            $table->string('receipt_number')->unique()->nullable()->after('payment_due_date')->comment('Numéro de reçu généré');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'total_fees',
                'amount_paid', 
                'balance_due',
                'payment_method',
                'payment_reference',
                'payment_notes',
                'payment_status',
                'payment_due_date',
                'receipt_number'
            ]);
        });
    }
};
