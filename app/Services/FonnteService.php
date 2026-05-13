<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim OTP via WhatsApp
     */
    public function sendOtp(string $phone, string $otpCode): bool
    {
        // Format nomor: 08xxx → 628xxx
        $phone = $this->formatPhone($phone);

        $message = "🕌 *Wali Santri - UQI Smart System*\n\n"
            . "Kode verifikasi OTP Anda:\n\n"
            . "🔑 *{$otpCode}*\n\n"
            . "Kode berlaku selama 5 menit.\n"
            . "Jangan berikan kode ini kepada siapapun.\n\n"
            . "_Pesan ini dikirim otomatis, mohon tidak membalas._";

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                Log::info('OTP sent via Fonnte', [
                    'phone' => $phone,
                    'status' => 'success',
                ]);
                return true;
            }

            Log::warning('Fonnte OTP failed', [
                'phone' => $phone,
                'response' => $result,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Fonnte OTP error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Format nomor HP ke format internasional (62xxx)
     */
    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
