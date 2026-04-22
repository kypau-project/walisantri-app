@extends('layouts.admin')
@section('page-title', 'Manajemen Tagihan')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-secondary);font-size:13px;">Total: {{ $bills->total() }} tagihan</p>
    <a href="/admin/bills/create" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Tagihan</a>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr><th>Santri</th><th>Tagihan</th><th>Jumlah</th><th>Dibayar</th><th>Status</th><th>Jatuh Tempo</th></tr>
        </thead>
        <tbody>
        @foreach($bills as $bill)
        <tr>
            <td>{{ $bill->student->name ?? '-' }}</td>
            <td><strong>{{ $bill->title }}</strong><br><span style="font-size:11px;color:var(--text-secondary);">{{ ucfirst($bill->type) }}</span></td>
            <td>Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-{{ $bill->status === 'paid' ? 'success' : ($bill->status === 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst($bill->status) }}</span></td>
            <td>{{ $bill->due_date ? $bill->due_date->format('d M Y') : '-' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">{{ $bills->links() }}</div>
@endsection
