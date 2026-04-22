@extends('layouts.admin')
@section('page-title', 'Manajemen Pembayaran')

@section('content')
<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr><th>Trans ID</th><th>Santri</th><th>Tagihan</th><th>Jumlah</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
        <tr>
            <td><code style="font-size:11px;">{{ $p->transaction_id }}</code></td>
            <td>{{ $p->student->name ?? '-' }}</td>
            <td>{{ $p->bill->title ?? '-' }}</td>
            <td><strong>Rp {{ number_format($p->amount, 0, ',', '.') }}</strong></td>
            <td>{{ ucfirst($p->payment_method) }}</td>
            <td><span class="badge badge-{{ $p->status === 'success' ? 'success' : 'danger' }}">{{ ucfirst($p->status) }}</span></td>
            <td>{{ $p->paid_at ? $p->paid_at->format('d M Y H:i') : '-' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">{{ $payments->links() }}</div>
@endsection
