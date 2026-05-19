@extends('layouts.admin')
@section('page-title', 'Daftar Ujian')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-secondary);font-size:13px;">Total: {{ $exams->total() }} ujian</p>
    <a href="/admin/exams/create" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Ujian</a>
</div>

@if (session('success'))
<div class="alert alert-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Mapel</th>
                <th>Ustadz</th>
                <th>Tanggal</th>
                <th>Durasi</th>
                <th>Soal</th>
                <th>Peserta</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($exams as $exam)
        <tr>
            <td><strong>{{ $exam->title }}</strong></td>
            <td>{{ $exam->subject }}</td>
            <td>{{ $exam->teacher_name ?? '-' }}</td>
            <td>{{ $exam->exam_date->format('d M Y') }}</td>
            <td>{{ $exam->duration_minutes }}m</td>
            <td>{{ $exam->questions_count }}</td>
            <td>{{ $exam->students_count }}</td>
            <td>
                @php
                $statusColors = ['draft' => 'warning', 'active' => 'info', 'completed' => 'success', 'archived' => 'secondary'];
                $statusLabels = ['draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai', 'archived' => 'Arsip'];
                @endphp
                <span class="badge badge-{{ $statusColors[$exam->status] ?? 'info' }}">{{ $statusLabels[$exam->status] ?? $exam->status }}</span>
            </td>
            <td>
                <div style="display:flex;gap:6px;">
                    <a href="/admin/exams/{{ $exam->id }}" class="btn btn-secondary btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                    <a href="/admin/exams/{{ $exam->id }}/questions" class="btn btn-primary btn-sm" title="Soal"><i class="fas fa-list-ol"></i></a>
                    <a href="/admin/exams/{{ $exam->id }}/results" class="btn btn-secondary btn-sm" title="Hasil"><i class="fas fa-chart-bar"></i></a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada ujian.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">{{ $exams->links() }}</div>
@endsection
