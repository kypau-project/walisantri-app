<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\StudentResource;
use App\Models\OtpVerification;
use App\Models\Student;
use App\Models\User;
use App\Models\Saving;
use App\Services\FonnteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user via nama santri atau NIS
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $identifier = $request->identifier;
        $password = $request->password;

        // Cek admin login (via username)
        $adminUser = User::where('username', $identifier)->where('role', 'admin')->first();
        if ($adminUser && Hash::check($password, $adminUser->password)) {
            $token = $adminUser->createToken('auth-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'user' => [
                        'id' => $adminUser->id,
                        'name' => $adminUser->name,
                        'username' => $adminUser->username,
                        'role' => $adminUser->role,
                    ],
                    'token' => $token,
                ],
            ]);
        }

        // Cari student berdasarkan NIS atau nama (case-insensitive)
        $student = Student::where('nis', $identifier)
            ->orWhereRaw('LOWER(name) = ?', [strtolower($identifier)])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan.',
            ], 401);
        }

        if (!$student->isClaimed()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun wali santri belum terdaftar. Silakan registrasi terlebih dahulu.',
            ], 401);
        }

        $user = $student->user;

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama santri/NIS atau password salah.',
            ], 401);
        }

        // Cek verifikasi OTP
        if (!$user->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun belum diverifikasi. Silakan verifikasi OTP terlebih dahulu.',
                'requires_verification' => true,
                'data' => [
                    'user_id' => $user->id,
                    'phone' => $user->phone,
                    'verify_otp_endpoint' => '/api/verify-otp',
                    'resend_otp_endpoint' => '/api/resend-otp',
                ],
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role,
                    'phone' => $user->phone,
                ],
                'student' => new StudentResource($student),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Register wali santri — cocokkan dengan data santri yang sudah ada
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Cari student berdasarkan nama DAN NIS (case-insensitive)
        $student = Student::whereRaw('LOWER(name) = ?', [strtolower($request->student_name)])
            ->where('nis', $request->nis)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan. Pastikan nama lengkap dan NIS sesuai dengan data pesantren.',
            ], 422);
        }

        $student->load('user', 'savingAccount');

        // Cek apakah sudah diklaim wali lain
        if ($student->isClaimed()) {
            if (!$student->user->isVerified()) {
                // Lepaskan claim lama yang belum verifikasi agar bisa registrasi ulang
                $oldUser = $student->user;
                $student->update(['user_id' => null]);
                $oldUser->delete();
                $student->unsetRelation('user');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri ini sudah terdaftar oleh wali lain.',
                ], 422);
            }
        }

        // Buat user wali
        $user = User::create([
            'name' => $request->student_name,
            'username' => $request->nis,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'wali',
        ]);

        // Hubungkan student ke user
        $student->update(['user_id' => $user->id]);

        // Buat tabungan jika belum ada
        if (!$student->savingAccount) {
            Saving::create([
                'student_id' => $student->id,
                'balance' => 0,
            ]);
        }

        // Generate OTP & kirim via WhatsApp
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerification::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Kirim OTP via WhatsApp (Fonnte)
        $fonnte = new FonnteService();
        $sent = $fonnte->sendOtp($request->phone, $otpCode);

        return response()->json([
            'success' => true,
            'message' => $sent
                ? 'Registrasi berhasil. Kode OTP telah dikirim ke WhatsApp Anda.'
                : 'Registrasi berhasil. Kode OTP gagal dikirim, silakan minta kirim ulang.',
            'data' => [
                'user_id' => $user->id,
                'phone' => $request->phone,
                'otp_sent' => $sent,
                'requires_verification' => true,
                'next_step' => [
                    'verify_otp_endpoint' => '/api/verify-otp',
                    'resend_otp_endpoint' => '/api/resend-otp',
                    'otp_expires_in_seconds' => 300,
                ],
            ],
        ], 201);
    }

    /**
     * Verifikasi kode OTP
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($request->user_id);

        // Cari OTP terbaru yang belum terverifikasi
        $otp = OtpVerification::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan. Silakan minta kirim ulang.',
            ], 422);
        }

        if ($otp->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kadaluarsa. Silakan minta kirim ulang.',
            ], 422);
        }

        if ($otp->hasMaxAttempts()) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan minta kirim ulang OTP.',
            ], 429);
        }

        // Increment attempts
        $otp->increment('attempts');

        if ($otp->otp_code !== $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah. Sisa percobaan: ' . (5 - $otp->attempts),
            ], 422);
        }

        // Verifikasi berhasil
        $otp->update(['verified_at' => now()]);
        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil! Akun Anda sudah aktif.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role,
                    'phone' => $user->phone,
                ],
                'student' => $user->student ? new StudentResource($user->student) : null,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun sudah terverifikasi.',
            ], 422);
        }

        // Cek rate limit: 1 OTP per 60 detik
        $lastOtp = OtpVerification::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
            $remaining = 60 - $lastOtp->created_at->diffInSeconds(now());
            return response()->json([
                'success' => false,
                'message' => "Tunggu {$remaining} detik sebelum mengirim ulang OTP.",
            ], 429);
        }

        // Generate OTP baru & kirim via WhatsApp
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerification::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
        ]);

        $fonnte = new FonnteService();
        $sent = $fonnte->sendOtp($user->phone, $otpCode);

        return response()->json([
            'success' => true,
            'message' => $sent
                ? 'Kode OTP baru telah dikirim ke WhatsApp Anda.'
                : 'Gagal mengirim OTP. Silakan coba lagi.',
            'data' => [
                'phone' => $user->phone,
                'otp_sent' => $sent,
            ],
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('student');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'phone' => $user->phone,
                'is_verified' => $user->isVerified(),
                'student' => $user->student ? new StudentResource($user->student) : null,
            ],
        ]);
    }
}
