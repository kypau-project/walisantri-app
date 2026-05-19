@extends('layouts.admin')
@section('page-title', $exam->title)

@section('content')
@if (session('success'))
<div class="alert alert-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="/admin/exams" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    <a href="/admin/exams/{{ $exam->id }}/edit" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="/admin/exams/{{ $exam->id }}/questions" class="btn btn-primary btn-sm"><i class="fas fa-list-ol"></i> Kelola Soal ({{ $exam->questions_count }})</a>
    <a href="/admin/exams/{{ $exam->id }}/results" class="btn btn-secondary btn-sm"><i class="fas fa-chart-bar"></i> Hasil</a>
    <form method="POST" action="/admin/exams/{{ $exam->id }}" onsubmit="return confirm('Hapus ujian ini beserta soal dan jawaban?')" style="margin-left:auto;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
    </form>
</div>

<div class="admin-card">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <h4 style="margin-bottom:12px;color:var(--primary-dark);"><i class="fas fa-info-circle"></i> Informasi Ujian</h4>
            <div class="profile-item"><span class="label">Judul</span><span class="value">{{ $exam->title }}</span></div>
            <div class="profile-item"><span class="label">Mata Pelajaran</span><span class="value">{{ $exam->subject }}</span></div>
            <div class="profile-item"><span class="label">Nama Ustadz</span><span class="value">{{ $exam->teacher_name ?? '-' }}</span></div>
            <div class="profile-item"><span class="label">Tanggal</span><span class="value">{{ $exam->exam_date->format('d M Y') }}</span></div>
            <div class="profile-item"><span class="label">Jam</span><span class="value">{{ $exam->start_time ?? '00:00' }} — {{ $exam->end_time ?? '23:59' }}</span></div>
            <div class="profile-item"><span class="label">Durasi</span><span class="value">{{ $exam->duration_minutes }} menit</span></div>
        </div>
        <div>
            <h4 style="margin-bottom:12px;color:var(--primary-dark);"><i class="fas fa-cog"></i> Pengaturan</h4>
            <div class="profile-item"><span class="label">Status</span>
                <span class="value">
                    @php
                    $sc = ['draft'=>'warning','active'=>'info','completed'=>'success','archived'=>'secondary'];
                    $sl = ['draft'=>'Draft','active'=>'Aktif','completed'=>'Selesai','archived'=>'Arsip'];
                    @endphp
                    <span class="badge badge-{{ $sc[$exam->status] ?? 'info' }}">{{ $sl[$exam->status] ?? $exam->status }}</span>
                </span>
            </div>
            <div class="profile-item"><span class="label">Acak Soal</span><span class="value">{{ $exam->shuffle_questions ? '✅ Ya' : '❌ Tidak' }}</span></div>
            <div class="profile-item"><span class="label">Tampil Hasil</span><span class="value">{{ $exam->show_result ? '✅ Ya' : '❌ Tidak' }}</span></div>
            <div class="profile-item"><span class="label">Jumlah Soal</span><span class="value">{{ $exam->questions_count }}</span></div>
            <div class="profile-item"><span class="label">Total Poin</span><span class="value">{{ $exam->questions->sum('points') }}</span></div>
            <div class="profile-item"><span class="label">Peserta</span><span class="value">{{ $exam->students->count() }} santri</span></div>
        </div>
    </div>

    @if($exam->description)
    <div style="margin-top:16px;padding:12px;background:#F8FAFC;border-radius:var(--radius-sm);font-size:13px;color:var(--text-secondary);">
        <strong>Instruksi:</strong> {{ $exam->description }}
    </div>
    @endif
</div>

<!-- Participants -->
<div class="admin-card">
    <h4 style="margin-bottom:12px;"><i class="fas fa-users" style="color:var(--primary);"></i> Daftar Peserta ({{ $exam->students->count() }})</h4>
    <table class="data-table">
        <thead>
            <tr><th>Nama</th><th>No. Induk</th><th>Kelas</th><th>Status</th><th>Nilai</th></tr>
        </thead>
        <tbody>
        @foreach($exam->students as $s)
        <tr>
            <td>{{ $s->name }}</td>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->class }}</td>
            <td>
                @php
                $psc = ['not_started'=>'secondary','in_progress'=>'warning','completed'=>'success','missed'=>'danger'];
                $psl = ['not_started'=>'Belum Mulai','in_progress'=>'Sedang Mengerjakan','completed'=>'Selesai','missed'=>'Terlewat'];
                @endphp
                <span class="badge badge-{{ $psc[$s->pivot->status] ?? 'info' }}">{{ $psl[$s->pivot->status] ?? $s->pivot->status }}</span>
            </td>
            <td>
                @if($s->pivot->status === 'completed')
                <strong style="color:{{ $s->pivot->score >= 70 ? 'var(--success)' : 'var(--danger)' }}">{{ $s->pivot->score }}</strong>
                @else - @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
