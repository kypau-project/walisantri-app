@extends('layouts.app')
@section('title', 'Ujian Online')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>📝 Ujian Online</h2>
            <p>Daftar ujian santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if($exams->count() > 0)

    @php
        $activeExams = $exams->whereIn('status', ['active', 'upcoming']);
        $completedExams = $exams->where('status', 'completed');
    @endphp

    <!-- Active / Upcoming Exams -->
    @if($activeExams->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ujian Mendatang</h3>
            <span class="badge badge-warning">{{ $activeExams->count() }} ujian</span>
        </div>

        @foreach($activeExams as $exam)
        <div class="list-item" style="flex-wrap:wrap;">
            <div class="list-icon" style="background:{{ $exam->status === 'active' ? '#DCFCE7' : '#DBEAFE' }};color:{{ $exam->status === 'active' ? '#15803D' : '#2563EB' }};">
                <i class="fas fa-{{ $exam->status === 'active' ? 'play-circle' : 'calendar' }}"></i>
            </div>
            <div class="list-content">
                <h4>{{ $exam->title }}</h4>
                <p>
                    {{ $exam->subject }} · {{ $exam->duration_minutes }} menit
                    <br>
                    <span class="badge badge-{{ $exam->status === 'active' ? 'success' : 'info' }}">
                        {{ $exam->status === 'active' ? '🟢 Sedang Berlangsung' : '📅 ' . $exam->exam_date->format('d M Y') }}
                    </span>
                </p>
            </div>
            <div style="width:100%;margin-top:10px;padding-left:58px;">
                @if($exam->status === 'active' && $exam->pivot->status !== 'completed')
                <a href="{{ $exam->exam_url ?? '#' }}" target="_blank" class="btn btn-primary btn-sm btn-block" id="start-exam-{{ $exam->id }}">
                    <i class="fas fa-external-link-alt"></i> Mulai Ujian
                </a>
                @elseif($exam->pivot->status === 'completed')
                <div style="text-align:center;">
                    <span class="badge badge-success"><i class="fas fa-check"></i> Selesai — Nilai: {{ $exam->pivot->score }}</span>
                </div>
                @else
                <span style="font-size:12px;color:var(--text-muted);">Ujian belum dimulai</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Completed Exams -->
    @if($completedExams->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ujian Selesai</h3>
            <span class="badge badge-secondary">{{ $completedExams->count() }} ujian</span>
        </div>

        @foreach($completedExams as $exam)
        <div class="list-item">
            <div class="list-icon" style="background:#F1F5F9;color:#64748B;">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="list-content">
                <h4>{{ $exam->title }}</h4>
                <p>{{ $exam->subject }} · {{ $exam->exam_date->format('d M Y') }}</p>
            </div>
            <div class="list-amount">
                <div class="amount" style="color:{{ ($exam->pivot->score ?? 0) >= 75 ? 'var(--success)' : 'var(--danger)' }};">
                    {{ $exam->pivot->score ?? '-' }}
                </div>
                <div class="date">Nilai</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="fas fa-pencil-alt"></i>
        <h3>Tidak Ada Ujian</h3>
        <p>Belum ada ujian yang dijadwalkan.</p>
    </div>
    @endif
</div>
@endsection
