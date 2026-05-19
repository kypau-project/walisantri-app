@extends('layouts.app')
@section('title', 'Ujian Online')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Ujian Online</h2>
            <p>Daftar ujian yang tersedia</p>
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

    @forelse($exams as $exam)
    <div class="card" style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    @php
                    $labelColors = [
                        'tersedia' => 'info',
                        'selesai' => 'success',
                        'terkunci' => 'secondary',
                        'terlewat' => 'danger',
                    ];
                    $labelIcons = [
                        'tersedia' => 'fa-unlock',
                        'selesai' => 'fa-check-circle',
                        'terkunci' => 'fa-lock',
                        'terlewat' => 'fa-times-circle',
                    ];
                    $labelTexts = [
                        'tersedia' => 'Tersedia',
                        'selesai' => 'Selesai',
                        'terkunci' => 'Terkunci',
                        'terlewat' => 'Terlewat',
                    ];
                    @endphp
                    <span class="badge badge-{{ $labelColors[$exam->display_label] ?? 'info' }}">
                        <i class="fas {{ $labelIcons[$exam->display_label] ?? 'fa-info' }}"></i>
                        {{ $labelTexts[$exam->display_label] ?? $exam->display_label }}
                    </span>
                </div>
                <h4 style="font-weight:700;margin-bottom:4px;">{{ $exam->title }}</h4>
                <p style="font-size:13px;color:var(--text-secondary);margin-bottom:4px;">
                    <i class="fas fa-book" style="width:16px;"></i> {{ $exam->subject }}
                    @if($exam->teacher_name) · <i class="fas fa-user-tie"></i> {{ $exam->teacher_name }} @endif
                </p>
                <p style="font-size:12px;color:var(--text-muted);">
                    <i class="fas fa-calendar"></i> {{ $exam->exam_date->format('d M Y') }}
                    @if($exam->start_time) · {{ $exam->start_time }} — {{ $exam->end_time }} @endif
                    · <i class="fas fa-clock"></i> {{ $exam->duration_minutes }} menit
                    · {{ $exam->questions_count }} soal
                </p>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:12px;">
                @if($exam->display_label === 'tersedia')
                    @if($exam->pivot->status === 'in_progress')
                    <a href="/exams/{{ $exam->id }}/take" class="btn btn-primary btn-sm">
                        <i class="fas fa-play"></i> Lanjutkan
                    </a>
                    @else
                    <a href="/exams/{{ $exam->id }}/start" class="btn btn-primary btn-sm">
                        <i class="fas fa-play"></i> Mulai
                    </a>
                    @endif
                @elseif($exam->display_label === 'selesai')
                    <div style="margin-bottom:6px;">
                        <span style="font-size:28px;font-weight:800;color:{{ $exam->pivot->score >= 70 ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $exam->pivot->score }}
                        </span>
                    </div>
                    @if($exam->show_result)
                    <a href="/exams/{{ $exam->id }}/result" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <h3>Belum Ada Ujian</h3>
        <p>Saat ini tidak ada ujian yang tersedia.</p>
    </div>
    @endforelse
</div>
@endsection
