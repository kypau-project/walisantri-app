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
                    Bayar
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

<!-- Pay Modal -->
<div class="modal-overlay" id="payModal">
    <div class="modal-content">
        <div class="modal-handle"></div>
        <h3 style="font-size:18px;font-weight:700;margin-bottom:16px;">💳 Bayar Tagihan</h3>
        <p id="payModalTitle" style="font-size:14px;color:var(--text-secondary);margin-bottom:20px;"></p>

        <form method="POST" action="/bills/pay" id="payForm">
            @csrf
            <input type="hidden" name="bill_id" id="payBillId">
            <div class="form-group">
                <label class="form-label">Jumlah Bayar (Rp)</label>
                <input type="number" name="amount" id="payAmount" class="form-input" placeholder="Masukkan jumlah" required min="1000">
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran</label>
                <select name="payment_method" class="form-select">
                    <option value="transfer">Transfer Bank</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="cash">Tunai</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="closePayModal()" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;" id="pay-submit-btn">
                    <i class="fas fa-paper-plane"></i> Bayar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openPayModal(billId, title, remaining) {
    document.getElementById('payBillId').value = billId;
    document.getElementById('payModalTitle').textContent = title + ' — Sisa: Rp ' + remaining.toLocaleString('id');
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
</script>
@endsection
