@extends('layouts.app')
@section('title', 'Tagihan')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>💰 Tagihan</h2>
            <p>Daftar tagihan santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if (session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if (session('info'))
    <div class="alert alert-info" style="background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif

    <!-- Summary -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEE2E2;color:#DC2626;"><i class="fas fa-clock"></i></div>
            <div class="stat-value">Rp {{ number_format($bills->whereIn('status', ['pending','partial','overdue'])->sum(fn($b) => $b->amount - $b->paid_amount), 0, ',', '.') }}</div>
            <div class="stat-label">Total Belum Bayar</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#DCFCE7;color:#15803D;"><i class="fas fa-check"></i></div>
            <div class="stat-value">{{ $bills->where('status', 'paid')->count() }}</div>
            <div class="stat-label">Tagihan Lunas</div>
        </div>
    </div>

    <!-- Desktop 2-col layout for pending + paid -->
    <div class="desktop-grid-2">

    <!-- Pending Bills -->
    @php $pendingBills = $bills->whereIn('status', ['pending', 'partial', 'overdue']); @endphp
    @if($pendingBills->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Belum Dibayar</h3>
            <span class="badge badge-warning">{{ $pendingBills->count() }} item</span>
        </div>

        @foreach($pendingBills as $bill)
        <div class="list-item">
            <div class="list-icon" style="background:{{ $bill->status === 'overdue' ? '#FEE2E2' : '#FEF3C7' }};color:{{ $bill->status === 'overdue' ? '#DC2626' : '#D97706' }};">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="list-content">
                <h4>{{ $bill->title }}</h4>
                <p>
                    @if($bill->status === 'overdue')
                    <span class="badge badge-danger">Jatuh Tempo</span>
                    @elseif($bill->status === 'partial')
                    <span class="badge badge-warning">Sebagian</span>
                    @else
                    <span class="badge badge-info">Menunggu</span>
                    @endif
                    @if($bill->due_date) · {{ $bill->due_date->format('d M Y') }} @endif
                </p>
            </div>
            <div class="list-amount">
                <div class="amount debit">Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}</div>
                <button class="btn btn-primary btn-sm" onclick="openPayModal({{ $bill->id }}, '{{ $bill->title }}', {{ $bill->amount - $bill->paid_amount }})" style="margin-top:6px;">
                    <i class="fas fa-credit-card"></i> Bayar
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Paid Bills -->
    @php $paidBills = $bills->where('status', 'paid'); @endphp
    @if($paidBills->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sudah Lunas</h3>
            <span class="badge badge-success">{{ $paidBills->count() }} item</span>
        </div>

        @foreach($paidBills->take(5) as $bill)
        <div class="list-item">
            <div class="list-icon" style="background:#DCFCE7;color:#15803D;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="list-content">
                <h4>{{ $bill->title }}</h4>
                <p><span class="badge badge-success">Lunas</span></p>
            </div>
            <div class="list-amount">
                <div class="amount" style="color:var(--text-muted);text-decoration:line-through;">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($bills->count() === 0)
    <div class="empty-state desktop-span-full">
        <i class="fas fa-file-invoice"></i>
        <h3>Tidak Ada Tagihan</h3>
        <p>Belum ada tagihan untuk saat ini.</p>
    </div>
    @endif

    </div><!-- /desktop-grid-2 -->
</div>

<!-- Pay Modal (simplified — no method picker, Midtrans handles it) -->
<div class="modal-overlay" id="payModal">
    <div class="modal-content">
        <div class="modal-handle"></div>
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:56px;height:56px;border-radius:16px;background:#F0FDFA;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <i class="fas fa-credit-card" style="font-size:24px;color:#0D9488;"></i>
            </div>
            <h3 style="font-size:18px;font-weight:700;margin-bottom:4px;">Bayar Tagihan</h3>
            <p id="payModalTitle" style="font-size:14px;color:var(--text-secondary);"></p>
        </div>

        <div class="form-group">
            <label class="form-label">Jumlah Bayar</label>
            <div style="position:relative;">
                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-weight:700;color:var(--text-secondary);font-size:14px;">Rp</span>
                <input type="number" id="payAmount" class="form-input" style="padding-left:42px;font-weight:700;font-size:16px;" placeholder="0" required min="1000">
            </div>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">Minimum Rp 1.000</p>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px;">
            <button type="button" class="btn btn-secondary" onclick="closePayModal()" style="flex:1;">Batal</button>
            <button type="button" class="btn btn-primary" style="flex:1;" id="paySubmitBtn" onclick="processPayment()">
                <i class="fas fa-lock"></i> Bayar Sekarang
            </button>
        </div>

        <div style="text-align:center;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <p style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">
                <i class="fas fa-shield-alt"></i> Pembayaran aman melalui
            </p>
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">🏦 Virtual Account</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">📱 E-Wallet</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">📷 QRIS</span>
                <span style="font-size:10px;padding:3px 8px;border-radius:6px;background:#F1F5F9;color:var(--text-secondary);font-weight:600;">🏪 Mitra</span>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="payLoading" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
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
let currentBillId = null;
let currentRemaining = 0;

function openPayModal(billId, title, remaining) {
    currentBillId = billId;
    currentRemaining = remaining;
    document.getElementById('payModalTitle').textContent = title;
    document.getElementById('payAmount').value = remaining;
    document.getElementById('payAmount').max = remaining;
    document.getElementById('payModal').classList.add('active');
}

function closePayModal() {
    document.getElementById('payModal').classList.remove('active');
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});

async function processPayment() {
    const amount = parseInt(document.getElementById('payAmount').value);
    const btn = document.getElementById('paySubmitBtn');
    const loading = document.getElementById('payLoading');

    if (!amount || amount < 1000) {
        alert('Jumlah minimum pembayaran Rp 1.000');
        return;
    }

    if (amount > currentRemaining) {
        alert('Jumlah melebihi sisa tagihan.');
        return;
    }

    // Close modal, show loading immediately
    closePayModal();
    loading.style.display = 'flex';

    try {
        const res = await fetch('/midtrans/snap-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                bill_id: currentBillId,
                amount: amount,
            }),
        });

        const data = await res.json();

        if (!data.success) {
            loading.style.display = 'none';
            alert(data.message || 'Gagal membuat pembayaran.');
            return;
        }

        // Hide loading, open Snap popup
        loading.style.display = 'none';

        window.snap.pay(data.data.snap_token, {
            onSuccess: function(result) {
                window.location.href = '/bills?status=success';
            },
            onPending: function(result) {
                window.location.href = '/bills?status=pending';
            },
            onError: function(result) {
                window.location.href = '/bills?status=error';
            },
            onClose: function() {
                window.location.reload();
            },
        });

    } catch (err) {
        loading.style.display = 'none';
        console.error(err);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>
@endsection
