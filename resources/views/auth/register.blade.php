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

    .error-text {
        color: var(--danger);
        font-size: 11px;
        margin-top: 4px;
    }

    /* Desktop responsive */
    @media (min-width: 768px) {
        .register-page {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .register-form-container {
            max-width: 600px;
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
            width: 560px;
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
        <h1>📝 Registrasi Baru</h1>
        <p>Daftarkan akun wali santri baru</p>
    </div>

    <div class="register-form-container">
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
                    <i class="fas fa-user"></i> Data Akun
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Wali</label>
                    <input type="text" name="name" class="form-input" placeholder="Nama wali santri"
                           value="{{ old('name') }}" required id="reg-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Username untuk login"
                           value="{{ old('username') }}" required id="reg-username">
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

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-child"></i> Data Santri
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Santri</label>
                    <input type="text" name="student_name" class="form-input" placeholder="Nama lengkap santri"
                           value="{{ old('student_name') }}" required id="reg-student-name">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-input" placeholder="Nomor Induk Santri"
                               value="{{ old('nis') }}" required id="reg-nis">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-select" id="reg-gender">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="class" class="form-input" placeholder="VII-A"
                               value="{{ old('class') }}" id="reg-class">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kamar</label>
                        <input type="text" name="room" class="form-input" placeholder="Al-Fatihah"
                               value="{{ old('room') }}" id="reg-room">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-phone"></i> Kontak
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">No. HP Ayah</label>
                        <input type="tel" name="father_phone" class="form-input" placeholder="08xxxxxxxxxx"
                               value="{{ old('father_phone') }}" id="reg-father-phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP Ibu</label>
                        <input type="tel" name="mother_phone" class="form-input" placeholder="08xxxxxxxxxx"
                               value="{{ old('mother_phone') }}" id="reg-mother-phone">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="register-btn">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
