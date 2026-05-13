<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\OtpVerification;
use App\Models\Saving;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isWali() && !$user->isVerified()) {
                return redirect('/verify-otp');
            }
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $identifier = $request->identifier;
        $password = $request->password;

        // Cek admin login (via username)
        $adminUser = User::where('username', $identifier)->where('role', 'admin')->first();
        if ($adminUser && Hash::check($password, $adminUser->password)) {
            Auth::login($adminUser, $request->remember);
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }

        // Cari student berdasarkan NIS atau nama
        $student = Student::where('nis', $identifier)
            ->orWhere('name', $identifier)
            ->first();

        if (!$student) {
            return back()->withErrors([
                'identifier' => 'Data santri tidak ditemukan.',
            ])->onlyInput('identifier');
        }

        if (!$student->isClaimed()) {
            return back()->withErrors([
                'identifier' => 'Akun wali santri belum terdaftar. Silakan registrasi terlebih dahulu.',
            ])->onlyInput('identifier');
        }

        $user = $student->user;

        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'identifier' => 'Nama santri/NIS atau password salah.',
            ])->onlyInput('identifier');
        }

        Auth::login($user, $request->remember);
        $request->session()->regenerate();

        // Redirect ke OTP jika belum terverifikasi
        if (!$user->isVerified()) {
            return redirect('/verify-otp');
        }

        return redirect('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // Cari student berdasarkan nama DAN NIS
        $student = Student::where('name', $request->student_name)
            ->where('nis', $request->nis)
            ->first();

        if (!$student) {
            return back()->withErrors([
                'nis' => 'Data santri tidak ditemukan. Pastikan nama lengkap dan NIS sesuai dengan data pesantren.',
            ])->withInput();
        }

        if ($student->isClaimed()) {
            return back()->withErrors([
                'nis' => 'Data santri ini sudah terdaftar oleh wali lain.',
            ])->withInput();
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

        // Generate OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerification::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
        ]);

        Auth::login($user);

        return redirect('/verify-otp')->with('otp_debug', $otpCode);
    }

    public function showVerifyOtp()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->isVerified()) {
            return redirect('/dashboard');
        }

        // Ambil OTP terbaru untuk info debug
        $latestOtp = OtpVerification::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        return view('auth.verify-otp', [
            'phone' => $user->phone,
            'otp_debug' => session('otp_debug') ?? ($latestOtp ? $latestOtp->otp_code : null),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        $otp = OtpVerification::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak ditemukan. Silakan minta kirim ulang.']);
        }

        if ($otp->isExpired()) {
            return back()->withErrors(['otp_code' => 'Kode OTP sudah kadaluarsa. Silakan minta kirim ulang.']);
        }

        if ($otp->hasMaxAttempts()) {
            return back()->withErrors(['otp_code' => 'Terlalu banyak percobaan. Silakan minta kirim ulang OTP.']);
        }

        $otp->increment('attempts');

        if ($otp->otp_code !== $request->otp_code) {
            $remaining = 5 - $otp->attempts;
            return back()->withErrors(['otp_code' => "Kode OTP salah. Sisa percobaan: {$remaining}"]);
        }

        // Verifikasi berhasil
        $otp->update(['verified_at' => now()]);
        $user->update(['phone_verified_at' => now()]);

        return redirect('/dashboard')->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    public function resendOtp()
    {
        $user = Auth::user();

        if ($user->isVerified()) {
            return redirect('/dashboard');
        }

        // Rate limit: 1 per 60 detik
        $lastOtp = OtpVerification::where('user_id', $user->id)->latest()->first();
        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
            $remaining = 60 - $lastOtp->created_at->diffInSeconds(now());
            return back()->with('warning', "Tunggu {$remaining} detik sebelum mengirim ulang OTP.");
        }

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerification::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
        ]);

        return redirect('/verify-otp')->with('otp_debug', $otpCode)->with('success', 'Kode OTP baru telah dikirim.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
