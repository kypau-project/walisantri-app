@extends('layouts.admin')
@section('page-title', 'Manajemen Raport')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-secondary);font-size:13px;">Total: {{ $reports->total() }} raport</p>
    <a href="/admin/reports/create" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Raport</a>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr><th>Santri</th><th>Semester</th><th>Tahun Ajaran</th><th>Rata-rata</th><th>Peringkat</th><th>Terbit</th></tr>
        </thead>
        <tbody>
        @foreach($reports as $r)
        <tr>
            <td>{{ $r->student->name ?? '-' }}</td>
            <td>{{ $r->semester }}</td>
            <td>{{ $r->academic_year }}</td>
            <td><strong>{{ $r->average_score ? number_format($r->average_score, 1) : '-' }}</strong></td>
            <td>{{ $r->rank ?? '-' }}</td>
            <td>{{ $r->published_at ? $r->published_at->format('d M Y') : 'Draft' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">{{ $reports->links() }}</div>
@endsection
