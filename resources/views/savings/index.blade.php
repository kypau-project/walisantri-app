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
        <div>
            <div class="form-group">
                <label class="form-label">Jumlah Top Up (Rp)</label>
                <input type="number" class="form-input" placeholder="Masukkan jumlah" required min="1000" id="topup-amount">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan (opsional)</label>
                <input type="text" class="form-input" placeholder="Contoh: Uang saku mingguan" id="topup-desc">
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                @foreach([10000, 25000, 50000, 100000, 200000] as $preset)
                <button type="button" class="btn btn-secondary btn-sm"
                        onclick="document.getElementById('topup-amount').value={{ $preset }}">
                    Rp {{ number_format($preset, 0, ',', '.') }}
                </button>
                @endforeach
            </div>

            <button type="button" class="btn btn-primary btn-block" id="topup-submit-btn" onclick="processTopup()">
                <i class="fas fa-paper-plane"></i> Top Up Sekarang
            </button>
        </div>

        <div style="text-align:center;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <p style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">
                <i class="fas fa-shield-alt"></i> Pembayaran aman melalui
            </p>
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">💳 Debit/Credit</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">🏦 Virtual Account</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">📱 E-Wallet</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">📷 QRIS</span>
            </div>
        </div>
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
                <p>
                    {{ $tx->transaction_id }}
                    @if(isset($tx->status) && $tx->status === 'pending')
                        <span class="badge badge-warning" style="margin-left:4px;">Pending</span>
                    @endif
                </p>
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

<!-- Loading Overlay -->
<div id="topupLoading" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;padding:40px;text-align:center;max-width:300px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="width:50px;height:50px;border:4px solid #E5E7EB;border-top-color:#0D9488;border-radius:50%;margin:0 auto 16px;animation:spin 0.8s linear infinite;"></div>
        <p style="font-weight:600;font-size:15px;color:#1F2937;margin-bottom:4px;">Menyiapkan Pembayaran</p>
        <p style="font-size:13px;color:#6B7280;">Menghubungkan ke Midtrans...</p>
    </div>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>

<!-- Midtrans Snap.js -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endsection

@section('scripts')
<script>
async function processTopup() {
    const amount = parseInt(document.getElementById('topup-amount').value);
    const description = document.getElementById('topup-desc').value;
    const loading = document.getElementById('topupLoading');

    if (!amount || amount < 1000) {
        Swal.fire({ icon: 'warning', title: 'Jumlah Tidak Valid', text: 'Jumlah minimum top up Rp 1.000', confirmButtonColor: '#0D9488' });
        return;
    }

    loading.style.display = 'flex';

    try {
        const res = await fetch('/midtrans/saving-snap-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                amount: amount,
                description: description || 'Top up saldo tabungan',
            }),
        });

        const data = await res.json();

        if (!data.success) {
            loading.style.display = 'none';
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal membuat token pembayaran.', confirmButtonColor: '#0D9488' });
            return;
        }

        loading.style.display = 'none';

        window.snap.pay(data.data.snap_token, {
            onSuccess: function(result) {
                Swal.fire({ icon: 'success', title: 'Top Up Berhasil!', text: 'Saldo tabungan akan segera bertambah.', confirmButtonColor: '#0D9488' }).then(() => {
                    window.location.href = '/savings';
                });
            },
            onPending: function(result) {
                Swal.fire({ icon: 'info', title: 'Menunggu Pembayaran', text: 'Saldo akan bertambah setelah pembayaran dikonfirmasi.', confirmButtonColor: '#0D9488' }).then(() => {
                    window.location.href = '/savings';
                });
            },
            onError: function(result) {
                Swal.fire({ icon: 'error', title: 'Top Up Gagal', text: 'Terjadi kesalahan saat memproses pembayaran.', confirmButtonColor: '#0D9488' }).then(() => {
                    window.location.href = '/savings';
                });
            },
            onClose: function() {
                window.location.reload();
            },
        });

    } catch (err) {
        loading.style.display = 'none';
        console.error(err);
        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Silakan coba lagi.', confirmButtonColor: '#0D9488' });
    }
}
</script>
@endsection
