@extends('layouts.app')
@section('title', 'Tabungan')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>🏦 Tabungan</h2>
            <p>Kelola tabungan santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if (session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-label"><i class="fas fa-wallet"></i> Saldo Tabungan</div>
        <div class="balance-value">Rp {{ number_format($saving?->balance ?? 0, 0, ',', '.') }}</div>
        <div class="balance-sub">{{ $student->name }} · {{ $student->nis }}</div>
    </div>

    <!-- Desktop 2-col layout -->
    <div class="desktop-grid-2">

    <!-- Quick Action -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:16px;"><i class="fas fa-plus-circle" style="color:var(--primary);"></i> Top Up Saldo</h3>
        <form method="POST" action="/savings/topup" id="topupForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Jumlah Top Up (Rp)</label>
                <input type="number" name="amount" class="form-input" placeholder="Masukkan jumlah" required min="1000" id="topup-amount">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan (opsional)</label>
                <input type="text" name="description" class="form-input" placeholder="Contoh: Uang saku mingguan" id="topup-desc">
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                @foreach([10000, 25000, 50000, 100000, 200000] as $preset)
                <button type="button" class="btn btn-secondary btn-sm"
                        onclick="document.getElementById('topup-amount').value={{ $preset }}">
                    Rp {{ number_format($preset, 0, ',', '.') }}
                </button>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="topup-submit-btn">
                <i class="fas fa-paper-plane"></i> Top Up Sekarang
            </button>
        </form>
    </div>

    <!-- Transaction History -->
    <div class="card">
        <div class="section-title">
            <h3>Riwayat Transaksi</h3>
        </div>

        @if($transactions->count() > 0)
        @foreach($transactions as $tx)
        <div class="list-item">
            <div class="list-icon" style="background:{{ $tx->type === 'topup' ? '#DCFCE7' : '#FEE2E2' }};color:{{ $tx->type === 'topup' ? '#15803D' : '#DC2626' }};">
                <i class="fas fa-{{ $tx->type === 'topup' ? 'arrow-down' : 'arrow-up' }}"></i>
            </div>
            <div class="list-content">
                <h4>{{ $tx->description ?? ($tx->type === 'topup' ? 'Top Up' : 'Penarikan') }}</h4>
                <p>{{ $tx->transaction_id }}</p>
            </div>
            <div class="list-amount">
                <div class="amount {{ $tx->type === 'topup' ? 'credit' : 'debit' }}">
                    {{ $tx->type === 'topup' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                </div>
                <div class="date">{{ $tx->created_at->format('d M Y') }}</div>
            </div>
        </div>
        @endforeach
        @else
        <div class="empty-state" style="padding:20px;">
            <i class="fas fa-inbox" style="font-size:32px;"></i>
            <p>Belum ada transaksi</p>
        </div>
        @endif
    </div>

    </div><!-- /desktop-grid-2 -->
</div>
@endsection
