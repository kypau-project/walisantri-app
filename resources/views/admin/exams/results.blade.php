@extends('layouts.admin')
@section('page-title', 'Hasil Ujian: ' . $exam->title)

@section('content')
<div style="margin-bottom:16px;">
    <a href="/admin/exams/{{ $exam->id }}" style="color:var(--primary);text-decoration:none;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
</div>

@if (session('success'))
<div class="alert alert-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="admin-card" style="margin-bottom:20px;">
    <h4>{{ $exam->title }} — {{ $exam->subject }}</h4>
    <p style="font-size:13px;color:var(--text-secondary);">
        {{ $exam->exam_date->format('d M Y') }} · {{ $exam->questions->count() }} soal · Total {{ $exam->questions->sum('points') }} poin
    </p>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Santri</th>
                <th>No. Induk</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($participants as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $p->name }}</strong></td>
            <td>{{ $p->nis }}</td>
            <td>{{ $p->class }}</td>
            <td>
                @php
                $sc = ['not_started'=>'secondary','in_progress'=>'warning','completed'=>'success','missed'=>'danger'];
                $sl = ['not_started'=>'Belum','in_progress'=>'Sedang','completed'=>'Selesai','missed'=>'Terlewat'];
                @endphp
                <span class="badge badge-{{ $sc[$p->pivot->status] ?? 'info' }}">{{ $sl[$p->pivot->status] ?? $p->pivot->status }}</span>
            </td>
            <td style="font-size:12px;">{{ $p->pivot->started_at ? \Carbon\Carbon::parse($p->pivot->started_at)->format('H:i:s') : '-' }}</td>
            <td style="font-size:12px;">{{ $p->pivot->finished_at ? \Carbon\Carbon::parse($p->pivot->finished_at)->format('H:i:s') : '-' }}</td>
            <td>
                @if($p->pivot->status === 'completed' && $p->pivot->score !== null)
                <strong style="font-size:16px;color:{{ $p->pivot->score >= 70 ? 'var(--success)' : ($p->pivot->score >= 50 ? 'var(--warning-dark, #D97706)' : 'var(--danger)') }}">
                    {{ $p->pivot->score }}
                </strong>
                @else - @endif
            </td>
            <td>
                @if($p->pivot->status === 'completed')
                <a href="/admin/exams/{{ $exam->id }}/grade/{{ $p->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-pen"></i> Koreksi
                </a>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

@php
$completedCount = $participants->where('pivot.status', 'completed')->count();
$avgScore = $participants->where('pivot.status', 'completed')->avg('pivot.score');
@endphp

<div class="admin-card" style="margin-top:16px;">
    <h4 style="margin-bottom:8px;"><i class="fas fa-chart-pie" style="color:var(--primary);"></i> Statistik</h4>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        <div style="text-align:center;padding:12px;background:var(--primary-50);border-radius:var(--radius-sm);">
            <div style="font-size:24px;font-weight:800;color:var(--primary-dark);">{{ $participants->count() }}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Total Peserta</div>
        </div>
        <div style="text-align:center;padding:12px;background:#DCFCE7;border-radius:var(--radius-sm);">
            <div style="font-size:24px;font-weight:800;color:#15803D;">{{ $completedCount }}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Selesai</div>
        </div>
        <div style="text-align:center;padding:12px;background:#FEF3C7;border-radius:var(--radius-sm);">
            <div style="font-size:24px;font-weight:800;color:#D97706;">{{ round($avgScore ?? 0, 1) }}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Rata-rata</div>
        </div>
        <div style="text-align:center;padding:12px;background:#FCE7F3;border-radius:var(--radius-sm);">
            <div style="font-size:24px;font-weight:800;color:#DB2777;">{{ $participants->where('pivot.status', 'missed')->count() }}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Terlewat</div>
        </div>
    </div>
</div>
@endsection
