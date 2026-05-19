@extends('layouts.app')
@section('title', 'Hasil Ujian')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/exams" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Hasil Ujian</h2>
            <p>{{ $exam->title }}</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    <!-- Score Card -->
    <div class="card" style="text-align:center;padding:30px 20px;">
        <div style="width:100px;height:100px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;
            background:{{ $pivot->pivot->score >= 70 ? '#DCFCE7' : '#FEE2E2' }};
            border:4px solid {{ $pivot->pivot->score >= 70 ? '#15803D' : '#DC2626' }};">
            <span style="font-size:32px;font-weight:900;color:{{ $pivot->pivot->score >= 70 ? '#15803D' : '#DC2626' }};">
                {{ $pivot->pivot->score }}
            </span>
        </div>
        <h3 style="margin-bottom:4px;">{{ $exam->title }}</h3>
        <p style="font-size:13px;color:var(--text-secondary);">{{ $exam->subject }} · {{ $exam->teacher_name }}</p>
        <div style="display:flex;gap:12px;justify-content:center;margin-top:12px;">
            <span class="badge badge-{{ $pivot->pivot->score >= 70 ? 'success' : 'danger' }}">
                {{ $pivot->pivot->score >= 70 ? '✅ Lulus' : '❌ Tidak Lulus' }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;max-width:300px;margin-left:auto;margin-right:auto;">
            <div style="padding:10px;background:#F8FAFC;border-radius:var(--radius-sm);">
                <div style="font-size:18px;font-weight:800;">{{ $pivot->pivot->total_points }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Poin Diperoleh</div>
            </div>
            <div style="padding:10px;background:#F8FAFC;border-radius:var(--radius-sm);">
                <div style="font-size:18px;font-weight:800;">{{ $exam->questions->sum('points') }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Total Poin</div>
            </div>
        </div>
    </div>

    @if($showDetail)
    <!-- Detailed Results -->
    @foreach($exam->questions as $i => $q)
    @php $answer = $answers->firstWhere('exam_question_id', $q->id); @endphp
    <div class="card" style="margin-bottom:8px;border-left:4px solid {{ ($answer && $answer->is_correct) ? 'var(--success)' : (($answer && $answer->is_correct === false) ? 'var(--danger)' : 'var(--warning)') }};">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <span style="background:var(--primary-50);color:var(--primary-dark);padding:2px 10px;border-radius:100px;font-size:12px;font-weight:700;">#{{ $i + 1 }}</span>
            <span style="font-size:12px;color:var(--text-muted);">{{ $q->points }} poin</span>
            @if($answer)
            <span style="font-size:12px;font-weight:600;color:{{ $answer->is_correct ? 'var(--success)' : ($answer->is_correct === false ? 'var(--danger)' : 'var(--warning)') }};">
                {{ $answer->is_correct ? '✅ Benar' : ($answer->is_correct === false ? '❌ Salah' : '⏳ Belum dikoreksi') }}
                · {{ $answer->points_earned }}/{{ $q->points }} poin
            </span>
            @else
            <span style="font-size:12px;color:var(--text-muted);">— Tidak dijawab</span>
            @endif
        </div>
        <p style="font-size:14px;line-height:1.6;margin-bottom:8px;">{{ $q->question_text }}</p>

        @if($q->question_type === 'multiple_choice' && $q->options)
        <div style="display:grid;gap:4px;">
            @foreach($q->options as $opt)
            @php
                $isCorrectOpt = strtoupper($opt['key']) === strtoupper($q->correct_answer ?? '');
                $isChosen = $answer && strtoupper($answer->answer_text ?? '') === strtoupper($opt['key']);
            @endphp
            <div style="padding:4px 10px;border-radius:6px;font-size:13px;
                {{ $isCorrectOpt ? 'background:#DCFCE7;color:#15803D;font-weight:600;' : ($isChosen ? 'background:#FEE2E2;color:#DC2626;' : 'color:var(--text-secondary);') }}">
                <strong>{{ $opt['key'] }}.</strong> {{ $opt['text'] }}
                @if($isCorrectOpt) <i class="fas fa-check-circle"></i> @endif
                @if($isChosen && !$isCorrectOpt) <i class="fas fa-times-circle"></i> jawaban Anda @endif
            </div>
            @endforeach
        </div>
        @elseif($q->question_type === 'essay' && $answer)
        <div style="background:#F8FAFC;padding:10px;border-radius:6px;font-size:13px;">
            <strong>Jawaban Anda:</strong><br>
            <p style="white-space:pre-wrap;margin-top:4px;">{{ $answer->answer_text }}</p>
        </div>
        @endif
    </div>
    @endforeach
    @else
    <div class="card" style="text-align:center;padding:30px;">
        <i class="fas fa-lock" style="font-size:32px;color:var(--text-muted);margin-bottom:12px;"></i>
        <h4>Detail Tidak Ditampilkan</h4>
        <p style="font-size:13px;color:var(--text-secondary);">Ustadz tidak menampilkan detail jawaban untuk ujian ini.</p>
    </div>
    @endif
</div>
@endsection
