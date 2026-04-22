@extends('layouts.app')
@section('title', 'Profil Santri')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Profil Santri</h2>
            <p>Informasi lengkap santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if($student)
    <!-- Student Card -->
    <div class="card" style="text-align:center;padding:30px 20px;">
        <div class="user-avatar" style="width:70px;height:70px;font-size:28px;margin:0 auto 14px;border-radius:20px;">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <h3 style="font-size:20px;font-weight:800;margin-bottom:4px;">{{ $student->name }}</h3>
        <p style="color:var(--text-secondary);font-size:13px;">NIS: {{ $student->nis }}</p>
        <div style="display:flex;gap:8px;justify-content:center;margin-top:12px;">
            <span class="badge badge-success"><i class="fas fa-check"></i> {{ ucfirst($student->status) }}</span>
            <span class="badge badge-info"><i class="fas fa-venus-mars"></i> {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:8px;"><i class="fas fa-id-card" style="color:var(--primary);margin-right:8px;"></i> Data Santri</h3>

        <div class="profile-item">
            <span class="label">Nama Lengkap</span>
            <span class="value">{{ $student->name }}</span>
        </div>
        <div class="profile-item">
            <span class="label">NIS</span>
            <span class="value">{{ $student->nis }}</span>
        </div>
        <div class="profile-item">
            <span class="label">Kelas</span>
            <span class="value">{{ $student->class ?? '-' }}</span>
        </div>
        <div class="profile-item">
            <span class="label">Kamar</span>
            <span class="value">{{ $student->room ?? '-' }}</span>
        </div>
        <div class="profile-item">
            <span class="label">Tanggal Lahir</span>
            <span class="value">{{ $student->birth_date ? $student->birth_date->format('d F Y') : '-' }}</span>
        </div>
        <div class="profile-item">
            <span class="label">Alamat</span>
            <span class="value" style="max-width:60%;">{{ $student->address ?? '-' }}</span>
        </div>
    </div>

    <!-- Contact Card -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:8px;"><i class="fas fa-phone" style="color:var(--primary);margin-right:8px;"></i> Kontak Orang Tua</h3>
        <div class="profile-item">
            <span class="label">No. WA Ayah</span>
            <span class="value">
                @if($student->father_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $student->father_phone) }}" target="_blank"
                   style="color:var(--success);text-decoration:none;">
                    <i class="fab fa-whatsapp"></i> {{ $student->father_phone }}
                </a>
                @else - @endif
            </span>
        </div>
        <div class="profile-item">
            <span class="label">No. WA Ibu</span>
            <span class="value">
                @if($student->mother_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $student->mother_phone) }}" target="_blank"
                   style="color:var(--success);text-decoration:none;">
                    <i class="fab fa-whatsapp"></i> {{ $student->mother_phone }}
                </a>
                @else - @endif
            </span>
        </div>
    </div>

    <!-- QR Code -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:16px;"><i class="fas fa-qrcode" style="color:var(--primary);margin-right:8px;"></i> Barcode ID Santri</h3>
        <div class="qr-container">
            <div style="margin-bottom:12px;">
                {!! QrCode::size(180)->generate($student->barcode_id ?? $student->nis) !!}
            </div>
            <p style="font-size:16px;font-weight:700;color:var(--text);letter-spacing:2px;">{{ $student->barcode_id ?? $student->nis }}</p>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">Tunjukkan QR code ini untuk identifikasi</p>
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-user-slash"></i>
        <h3>Data Santri Belum Ada</h3>
        <p>Silakan hubungi admin untuk mendaftarkan santri.</p>
    </div>
    @endif
</div>
@endsection
