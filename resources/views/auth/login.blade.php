@extends('layouts.app')
@section('title', 'Login')
@section('hide-nav', 'true')

@section('styles')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: linear-gradient(160deg, var(--primary-darker) 0%, var(--primary-dark) 30%, var(--primary) 60%, var(--primary-light) 100%);
        position: relative;
        overflow: hidden;
    }

    .login-page::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .login-page::after {
        content: '';
        position: absolute;
        bottom: -15%;
        left: -15%;
        width: 350px;
        height: 350px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }

    .login-hero {
        flex: 0 0 auto;
        text-align: center;
        padding: 60px 30px 40px;
        color: white;
        position: relative;
        z-index: 2;
    }

    .login-logo {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.15);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
        backdrop-filter: blur(20px);
        border: 2px solid rgba(255,255,255,0.2);
        animation: fadeIn 0.6s ease;
    }

    .login-hero h1 {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .login-hero p {
        font-size: 14px;
        opacity: 0.8;
        font-weight: 400;
    }

    .login-form-container {
        flex: 1;
        background: white;
        border-radius: 28px 28px 0 0;
        padding: 36px 28px 40px;
        position: relative;
        z-index: 2;
        animation: slideUp 0.4s ease;
    }

    .login-form-container h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 4px;
        color: var(--text);
    }

    .login-form-container .subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 28px;
    }

    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 16px;
    }

    .input-icon-wrap .form-input {
        padding-left: 46px;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 16px;
    }

    .form-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .form-footer a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }

    .form-footer a:hover { text-decoration: underline; }

    /* Desktop responsive */
    @media (min-width: 768px) {
        .login-page {
            align-items: center;
            justify-content: center;
        }

        .login-hero {
            padding: 0 30px 30px;
        }

        .login-form-container {
            flex: none;
            max-width: 440px;
            width: 100%;
            border-radius: 24px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
    }

    @media (min-width: 1200px) {
        .login-page {
            flex-direction: row;
        }

        .login-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-hero h1 { font-size: 36px; }
        .login-hero p { font-size: 16px; }
        .login-logo { width: 100px; height: 100px; font-size: 48px; border-radius: 28px; }

        .login-form-container {
            flex: none;
            width: 480px;
            min-height: 100vh;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 40px;
            margin-bottom: 0;
            box-shadow: -10px 0 40px rgba(0,0,0,0.1);
        }

        .login-form-container h2 { font-size: 26px; }
    }
</style>
@endsection

@section('content')
<div class="login-page">
    <div class="login-hero">
        <div class="login-logo">🕌</div>
        <h1>Wali Santri App</h1>
        <p>UQI Smart System</p>
    </div>

    <div class="login-form-container">
        <h2>Selamat Datang! 👋</h2>
        <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

        @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="/login" id="loginForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-input" placeholder="Masukkan username"
                           value="{{ old('username') }}" required autofocus id="username-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-input" placeholder="Masukkan password"
                           required id="password-input">
                    <button type="button" class="password-toggle" onclick="togglePassword()" id="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="login-btn">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div class="form-footer">
            Belum punya akun? <a href="/register">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password-input');
    const icon = document.querySelector('#toggle-password i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
