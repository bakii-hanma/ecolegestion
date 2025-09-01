<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Moov Money',
                'code' => 'moov_money',
                'description' => 'Service de paiement mobile Moov Money',
                'logo_url' => '/images/payment/moov-money.png',
                'color' => '#007BFF',
                'is_active' => true,
                'is_test_mode' => true,
                'transaction_fee' => 1.0, // 1%
                'fixed_fee' => 25.00, // 25 FCFA
                'min_amount' => 100.00,
                'max_amount' => 1000000.00,
                'config' => [
                    'api_url' => 'https://api.moov-africa.com/money',
                    'merchant_id' => 'STUDIAGABON',
                    'api_key' => 'test_key_moov',
                    'callback_url' => '/api/payments/moov/callback'
                ]
            ],
            [
                'name' => 'Airtel Money',
                'code' => 'airtel_money',
                'description' => 'Service de paiement mobile Airtel Money',
                'logo_url' => '/images/payment/airtel-money.png',
                'color' => '#DC3545',
                'is_active' => true,
                'is_test_mode' => true,
                'transaction_fee' => 1.2, // 1.2%
                'fixed_fee' => 30.00, // 30 FCFA
                'min_amount' => 100.00,
                'max_amount' => 750000.00,
                'config' => [
                    'api_url' => 'https://api.airtel.com/money',
                    'merchant_id' => 'STUDIAGABON',
                    'api_key' => 'test_key_airtel',
                    'callback_url' => '/api/payments/airtel/callback'
                ]
            ]
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['code' => $gateway['code']],
                $gateway
            );
        }

        $this->command->info('✅ Passerelles de paiement créées avec succès !');
    }
}
