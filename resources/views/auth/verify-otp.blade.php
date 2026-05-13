@extends('layouts.app')
@section('title', 'Verifikasi OTP')
@section('hide-nav', 'true')

@section('styles')
<style>
    .otp-page {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(160deg, var(--primary-darker) 0%, var(--primary-dark) 30%, var(--primary) 60%, var(--primary-light) 100%);
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .otp-page::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .otp-card {
        background: white;
        border-radius: 24px;
        padding: 40px 32px;
        width: 100%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        position: relative;
        z-index: 2;
        animation: slideUp 0.4s ease;
    }

    .otp-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
    }

    .otp-card h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text);
    }

    .otp-card .subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 8px;
        line-height: 1.5;
    }

    .phone-display {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--primary-50);
        color: var(--primary-dark);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 24px;
    }

    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 24px;
    }

    .otp-input {
        width: 50px;
        height: 58px;
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        font-family: var(--font);
        border: 2px solid var(--border);
        border-radius: 14px;
        outline: none;
        transition: all 0.2s;
        color: var(--text);
        background: #FAFAFA;
    }

    .otp-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        background: white;
    }

    .otp-input.filled {
        border-color: var(--primary);
        background: var(--primary-50);
    }

    .resend-section {
        margin-top: 20px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .resend-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .resend-link:hover {
        text-decoration: underline;
    }

    .resend-link.disabled {
        color: var(--text-muted);
        pointer-events: none;
    }

    .debug-banner {
        background: linear-gradient(135deg, #FEF3C7, #FFFBEB);
        border: 1px solid #FCD34D;
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 12px;
        color: #92400E;
        line-height: 1.5;
    }

    .debug-banner .otp-debug-code {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 6px;
        color: #B45309;
        display: block;
        margin-top: 4px;
    }

    .timer {
        font-weight: 600;
        color: var(--primary);
    }

    /* Hidden real input */
    .otp-hidden-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="otp-page">
    <div class="otp-card">
        <div class="otp-icon">📱</div>
        <h2>Verifikasi Nomor HP</h2>
        <p class="subtitle">Masukkan 6 digit kode OTP yang dikirim ke</p>
        <div class="phone-display">
            <i class="fas fa-phone"></i>
            {{ substr($phone, 0, 4) }}****{{ substr($phone, -3) }}
        </div>

        @if ($errors->any())
        <div class="alert alert-error" style="text-align:left;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success" style="text-align:left;margin-bottom:16px;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if (session('warning'))
        <div class="alert alert-warning" style="text-align:left;margin-bottom:16px;">
            <i class="fas fa-clock"></i>
            {{ session('warning') }}
        </div>
        @endif

        @if ($otp_debug)
        <div class="debug-banner">
            <i class="fas fa-bug"></i> <strong>Mode Simulasi</strong> — Kode OTP Anda:
            <span class="otp-debug-code">{{ $otp_debug }}</span>
        </div>
        @endif

        <form method="POST" action="/verify-otp" id="otpForm">
            @csrf
            <input type="hidden" name="otp_code" id="otpHiddenInput" value="">

            <div class="otp-inputs" id="otpInputs">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="0" autofocus>
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="1">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="2">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="3">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="4">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="5">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="verifyBtn">
                <i class="fas fa-shield-alt"></i> Verifikasi
            </button>
        </form>

        <div class="resend-section">
            <p>Belum menerima kode?</p>
            <form method="POST" action="/resend-otp" style="display:inline;">
                @csrf
                <button type="submit" class="resend-link" id="resendBtn" style="background:none;border:none;font-family:var(--font);font-size:13px;">
                    Kirim Ulang OTP
                </button>
            </form>
            <p style="margin-top:16px;">
                <a href="/logout" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                   style="color:var(--text-muted);font-size:12px;text-decoration:none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </p>
            <form id="logoutForm" action="/logout" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-input');
    const hiddenInput = document.getElementById('otpHiddenInput');
    const form = document.getElementById('otpForm');

    function updateHiddenInput() {
        let code = '';
        inputs.forEach(input => { code += input.value; });
        hiddenInput.value = code;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1) {
                this.classList.add('filled');
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }

            updateHiddenInput();

            // Auto-submit when all filled
            if (hiddenInput.value.length === 6) {
                form.submit();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                inputs[index - 1].classList.remove('filled');
                updateHiddenInput();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            for (let i = 0; i < Math.min(pasted.length, 6); i++) {
                inputs[i].value = pasted[i];
                inputs[i].classList.add('filled');
            }
            if (pasted.length >= 6) {
                inputs[5].focus();
            } else if (pasted.length > 0) {
                inputs[Math.min(pasted.length, 5)].focus();
            }
            updateHiddenInput();
            if (hiddenInput.value.length === 6) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
