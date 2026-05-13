@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="app-header">
    <div class="header-top">
        <div class="header-brand">
            <div class="logo">🕌</div>
            <div>
                <h1>Wali Santri</h1>
                <span>UQI Smart System</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="/profile" class="header-btn" id="btn-notif" title="Notifikasi">
                <i class="fas fa-bell"></i>
            </a>
            <form method="POST" action="/logout" style="margin:0">
                @csrf
                <button type="submit" class="header-btn" id="btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    @if($student)
    <div class="header-user">
        <div class="user-avatar-photo">
            @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <span class="avatar-fallback" style="display:none;">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            @else
                <span class="avatar-fallback">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="user-info">
            <h3>{{ $student->name }}</h3>
            <p>{{ $student->class ?? '-' }} · {{ $student->room ?? '-' }} · No. Induk: {{ $student->nis }}</p>
        </div>
    </div>
    @endif
</div>

<div class="app-content fade-in">
    @if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    @if($student)
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF3C7;color:#D97706;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-value">{{ $pendingBills }}</div>
            <div class="stat-label">Tagihan Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#DCFCE7;color:#15803D;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            <div class="stat-label">Total Dibayar</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#DBEAFE;color:#2563EB;">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($savingBalance, 0, ',', '.') }}</div>
            <div class="stat-label">Saldo Tabungan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FCE7F3;color:#DB2777;">
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div class="stat-value">{{ $upcomingExams }}</div>
            <div class="stat-label">Ujian Mendatang</div>
        </div>
    </div>
    @endif

    <!-- Desktop 2-col layout for menu + pending bills -->
    <div class="desktop-grid-2">

    <!-- Menu Grid -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Menu Utama</h3>
        </div>
        <div class="menu-grid">
            <a href="/profile" class="menu-item" id="menu-profile">
                <div class="menu-icon teal"><i class="fas fa-user"></i></div>
                <span class="menu-label">Profil Santri</span>
            </a>
            <a href="/bills" class="menu-item" id="menu-bills">
                <div class="menu-icon amber"><i class="fas fa-file-invoice-dollar"></i></div>
                <span class="menu-label">Tagihan</span>
            </a>
            <a href="/payments" class="menu-item" id="menu-payments">
                <div class="menu-icon blue"><i class="fas fa-history"></i></div>
                <span class="menu-label">Riwayat Bayar</span>
            </a>
            <a href="/savings" class="menu-item" id="menu-savings">
                <div class="menu-icon emerald"><i class="fas fa-piggy-bank"></i></div>
                <span class="menu-label">Tabungan</span>
            </a>
            <a href="/exams" class="menu-item" id="menu-exams">
                <div class="menu-icon rose"><i class="fas fa-pencil-alt"></i></div>
                <span class="menu-label">Ujian Online</span>
            </a>
            <a href="/reports" class="menu-item" id="menu-reports">
                <div class="menu-icon violet"><i class="fas fa-chart-bar"></i></div>
                <span class="menu-label">Raport</span>
            </a>
            <a href="/profile" class="menu-item" id="menu-qrcode">
                <div class="menu-icon cyan"><i class="fas fa-qrcode"></i></div>
                <span class="menu-label">QR Code</span>
            </a>
            <a href="/profile" class="menu-item" id="menu-info">
                <div class="menu-icon orange"><i class="fas fa-info-circle"></i></div>
                <span class="menu-label">Info</span>
            </a>
        </div>
    </div>

    <!-- Quick Payment Summary -->
    @if($student && $student->bills()->whereIn('status', ['pending', 'partial', 'overdue'])->count() > 0)
    <div class="card">
        <div class="section-title">
            <h3>⚡ Tagihan Menunggu</h3>
            <a href="/bills">Lihat Semua →</a>
        </div>
        @foreach($student->bills()->whereIn('status', ['pending', 'partial', 'overdue'])->orderBy('due_date')->take(5)->get() as $bill)
        <div class="list-item">
            <div class="list-icon" style="background:{{ $bill->status === 'overdue' ? '#FEE2E2' : '#FEF3C7' }};color:{{ $bill->status === 'overdue' ? '#DC2626' : '#D97706' }};">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="list-content">
                <h4>{{ $bill->title }}</h4>
                <p>
                    @if($bill->status === 'overdue')
                    <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Jatuh Tempo</span>
                    @elseif($bill->status === 'partial')
                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Sebagian</span>
                    @else
                    <span class="badge badge-info"><i class="fas fa-clock"></i> Menunggu</span>
                    @endif
                </p>
            </div>
            <div class="list-amount">
                <div class="amount debit">Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</div>
                <div class="date">{{ $bill->due_date ? $bill->due_date->format('d M Y') : '-' }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    </div><!-- /desktop-grid-2 -->
</div>

@section('styles')
<style>
.user-avatar-photo {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    overflow: hidden;
    background: var(--primary-100);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-avatar-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-avatar-photo .avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 800;
    color: white;
    background: var(--primary);
}
</style>
@endsection
@endsection
