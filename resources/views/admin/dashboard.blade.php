@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="icon" style="background:#CCFBF1;color:#0D9488;"><i class="fas fa-users"></i></div>
        <div class="value">{{ $stats['total_students'] }}</div>
        <div class="label">Total Santri</div>
    </div>
    <div class="admin-stat-card">
        <div class="icon" style="background:#DCFCE7;color:#15803D;"><i class="fas fa-user-check"></i></div>
        <div class="value">{{ $stats['active_students'] }}</div>
        <div class="label">Santri Aktif</div>
    </div>
    <div class="admin-stat-card">
        <div class="icon" style="background:#FEF3C7;color:#D97706;"><i class="fas fa-file-invoice"></i></div>
        <div class="value">{{ $stats['pending_bills'] }}</div>
        <div class="label">Tagihan Pending</div>
    </div>
    <div class="admin-stat-card">
        <div class="icon" style="background:#DBEAFE;color:#2563EB;"><i class="fas fa-money-bill-wave"></i></div>
        <div class="value">Rp {{ number_format($stats['total_payments'], 0, ',', '.') }}</div>
        <div class="label">Total Pembayaran</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="admin-card">
        <h3><i class="fas fa-clock" style="color:var(--primary);margin-right:6px;"></i> Pembayaran Terbaru</h3>
        @if(count($stats['recent_payments']) > 0)
        <table class="data-table">
            <thead><tr><th>Santri</th><th>Jumlah</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($stats['recent_payments'] as $p)
            <tr>
                <td>{{ $p->student->name ?? '-' }}</td>
                <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                <td><span class="badge badge-{{ $p->status === 'success' ? 'success' : 'warning' }}">{{ ucfirst($p->status) }}</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p style="color:var(--text-secondary);font-size:13px;">Belum ada pembayaran.</p>
        @endif
    </div>

    <div class="admin-card">
        <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:6px;"></i> Santri Terbaru</h3>
        @if(count($stats['recent_students']) > 0)
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Kelas</th><th>NIS</th></tr></thead>
            <tbody>
            @foreach($stats['recent_students'] as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->class }}</td>
                <td>{{ $s->nis }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p style="color:var(--text-secondary);font-size:13px;">Belum ada santri.</p>
        @endif
    </div>
</div>
@endsection
