<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private string $clientKey;
    private bool $isProduction;
    private string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Buat Snap Transaction — returns both token and redirect_url in ONE call
     */
    public function createTransaction(array $params): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withBasicAuth($this->serverKey, '')
                ->post($this->baseUrl . '/transactions', $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'token' => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                ];
            }

            Log::error('Midtrans Snap Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Shortcut: hanya ambil snap token
     */
    public function createSnapToken(array $params): ?string
    {
        $result = $this->createTransaction($params);
        return $result['token'] ?? null;
    }

    /**
     * Verifikasi signature notification dari Midtrans
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return $expected === $signatureKey;
    }

    /**
     * Get client key (untuk frontend Snap.js)
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Get Snap JS URL
     */
    public function getSnapJsUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Build standard transaction params
     */
    public function buildTransactionParams(string $orderId, int $amount, array $customer, array $items = []): array
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $customer['name'] ?? 'Wali Santri',
                'phone' => $customer['phone'] ?? '',
            ],
            'callbacks' => [
                'finish' => url('/midtrans/finish'),
            ],
            'enabled_payments' => [
                // Kartu Debit/Credit
                'credit_card',
                // Virtual Account
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va', 'echannel',
                // E-Wallet
                'gopay', 'shopeepay',
                // QRIS
                'qris',
                // Mitra/Agen
                'indomaret', 'alfamart',
            ],
        ];

        if (!empty($items)) {
            $params['item_details'] = $items;
        }

        return $params;
    }
}
