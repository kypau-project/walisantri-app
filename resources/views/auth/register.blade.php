@extends('layouts.app')
@section('title', 'Registrasi')
@section('hide-nav', 'true')

@section('styles')
<style>
    .register-page {
        min-height: 100vh;
        background: linear-gradient(160deg, var(--primary-darker) 0%, var(--primary-dark) 30%, var(--primary) 60%, var(--primary-light) 100%);
        position: relative;
        overflow: hidden;
    }

    .register-header {
        text-align: center;
        padding: 40px 30px 30px;
        color: white;
        position: relative;
        z-index: 2;
    }

    .register-header h1 { font-size: 24px; font-weight: 800; }
    .register-header p { font-size: 13px; opacity: 0.8; margin-top: 4px; }

    .register-form-container {
        background: white;
        border-radius: 28px 28px 0 0;
        padding: 30px 24px 40px;
        position: relative;
        z-index: 2;
        min-height: 60vh;
    }

    .form-section {
        margin-bottom: 20px;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title i {
        width: 28px;
        height: 28px;
        background: var(--primary-50);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--primary);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .input-hint {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .info-banner {
        background: linear-gradient(135deg, #DBEAFE, #EFF6FF);
        border: 1px solid #93C5FD;
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 12px;
        color: #1E40AF;
        line-height: 1.5;
    }

    .info-banner i {
        font-size: 16px;
        margin-top: 1px;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .register-page {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .register-form-container {
            max-width: 500px;
            width: 100%;
            border-radius: 24px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
    }

    @media (min-width: 1200px) {
        .register-page {
            flex-direction: row;
            min-height: 100vh;
        }

        .register-header {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .register-header h1 { font-size: 32px; }

        .register-form-container {
            flex: none;
            width: 500px;
            min-height: 100vh;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 36px;
            margin-bottom: 0;
            box-shadow: -10px 0 40px rgba(0,0,0,0.1);
        }
    }
</style>
@endsection

@section('content')
<div class="register-page">
    <div class="register-header">
        <a href="/login" style="color:white;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
        <h1>📝 Registrasi Wali Santri</h1>
        <p>Cocokkan data santri dari pesantren</p>
    </div>

    <div class="register-form-container">
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <div>
        Data santri diinput oleh pesantren. Wali santri hanya perlu mencocokkan <strong>nama lengkap</strong> dan <strong>No. Induk</strong> santri yang sudah terdaftar.
            </div>
        </div>

        @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST" action="/register" id="registerForm">
            @csrf

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-child"></i> Data Santri
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Santri</label>
                    <input type="text" name="student_name" class="form-input" placeholder="Nama sesuai data pesantren"
                           value="{{ old('student_name') }}" required id="reg-student-name">
                    <p class="input-hint">Contoh: Muhammad Rizki Fauzi</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Induk Santri</label>
                    <input type="text" name="nis" class="form-input" placeholder="Masukkan No. Induk"
                           value="{{ old('nis') }}" required id="reg-nis">
                    <p class="input-hint">Contoh: 2024001</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-phone"></i> Kontak Wali
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP Wali Santri</label>
                    <input type="tel" name="phone" class="form-input" placeholder="08xxxxxxxxxx"
                           value="{{ old('phone') }}" required id="reg-phone">
                    <p class="input-hint">Kode OTP akan dikirim ke nomor ini</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-lock"></i> Keamanan
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Min. 6 karakter"
                               required id="reg-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password"
                               required id="reg-password-confirm">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="register-btn">
                <i class="fas fa-user-plus"></i> Daftar & Verifikasi
            </button>
        </form>

        <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-secondary);">
            Sudah punya akun? <a href="/login" style="color:var(--primary);font-weight:600;text-decoration:none;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
