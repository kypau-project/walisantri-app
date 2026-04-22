@extends('layouts.admin')
@section('page-title', 'Manajemen Ujian')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-secondary);font-size:13px;">Total: {{ $exams->total() }} ujian</p>
    <a href="/admin/exams/create" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Ujian</a>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr><th>Judul</th><th>Mata Pelajaran</th><th>Tanggal</th><th>Durasi</th><th>Peserta</th><th>Status</th></tr>
        </thead>
        <tbody>
        @foreach($exams as $exam)
        <tr>
            <td><strong>{{ $exam->title }}</strong></td>
            <td>{{ $exam->subject }}</td>
            <td>{{ $exam->exam_date->format('d M Y') }}</td>
            <td>{{ $exam->duration_minutes }} menit</td>
            <td>{{ $exam->students_count }} santri</td>
            <td><span class="badge badge-{{ $exam->status === 'completed' ? 'success' : ($exam->status === 'active' ? 'info' : 'warning') }}">{{ ucfirst($exam->status) }}</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">{{ $exams->links() }}</div>
@endsection
