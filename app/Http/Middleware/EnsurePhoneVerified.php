<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isWali() && !$user->isVerified()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun belum diverifikasi. Silakan verifikasi OTP terlebih dahulu.',
                    'requires_verification' => true,
                ], 403);
            }
            return redirect('/verify-otp')->with('warning', 'Silakan verifikasi nomor HP Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
