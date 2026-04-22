@extends('layouts.app')
@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>📋 Riwayat Pembayaran</h2>
            <p>Histori transaksi pembayaran</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if($payments->count() > 0)
    <div class="card">
        @foreach($payments as $payment)
        <div class="list-item">
            <div class="list-icon" style="background:{{ $payment->status === 'success' ? '#DCFCE7' : '#FEE2E2' }};color:{{ $payment->status === 'success' ? '#15803D' : '#DC2626' }};">
                <i class="fas fa-{{ $payment->status === 'success' ? 'check-circle' : 'times-circle' }}"></i>
            </div>
            <div class="list-content">
                <h4>{{ $payment->bill?->title ?? 'Pembayaran' }}</h4>
                <p>
                    {{ $payment->transaction_id }}
                    <br>
                    <span class="badge badge-{{ $payment->status === 'success' ? 'success' : 'danger' }}">
                        {{ $payment->status === 'success' ? 'Berhasil' : 'Gagal' }}
                    </span>
                    ·
                    <span style="font-size:11px;color:var(--text-muted);">
                        {{ ucfirst($payment->payment_method) }}
                    </span>
                </p>
            </div>
            <div class="list-amount">
                <div class="amount credit">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                <div class="date">{{ $payment->paid_at ? $payment->paid_at->format('d M Y') : $payment->created_at->format('d M Y') }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-receipt"></i>
        <h3>Belum Ada Pembayaran</h3>
        <p>Riwayat pembayaran akan muncul di sini.</p>
    </div>
    @endif
</div>
@endsection
