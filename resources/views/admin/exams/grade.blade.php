@extends('layouts.admin')
@section('page-title', 'Koreksi: ' . $student->name)

@section('content')
<div style="margin-bottom:16px;">
    <a href="/admin/exams/{{ $exam->id }}/results" style="color:var(--primary);text-decoration:none;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Hasil
    </a>
</div>

<div class="admin-card" style="margin-bottom:16px;">
    <h4>Koreksi Jawaban: {{ $student->name }} ({{ $student->nis }})</h4>
    <p style="font-size:13px;color:var(--text-secondary);">{{ $exam->title }} — {{ $exam->subject }}</p>
</div>

<form method="POST" action="/admin/exams/{{ $exam->id }}/grade/{{ $student->id }}">
    @csrf

    @foreach($exam->questions as $i => $q)
    @php $answer = $answers->get($q->id); @endphp
    <div class="admin-card" style="margin-bottom:12px;border-left:4px solid {{ $q->question_type === 'multiple_choice' ? 'var(--primary)' : 'var(--warning)' }};">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <span style="background:var(--primary-50);color:var(--primary-dark);padding:2px 10px;border-radius:var(--radius-full);font-size:12px;font-weight:700;">#{{ $i + 1 }}</span>
            <span class="badge {{ $q->question_type === 'multiple_choice' ? 'badge-info' : 'badge-warning' }}" style="font-size:11px;">
                {{ $q->question_type === 'multiple_choice' ? 'PG' : 'Essay' }}
            </span>
            <span style="font-size:12px;color:var(--text-muted);">{{ $q->points }} poin</span>
        </div>

        <p style="font-size:14px;line-height:1.6;margin-bottom:10px;">{!! nl2br(e($q->question_text)) !!}</p>

        @if($q->question_type === 'multiple_choice' && $q->options)
        <div style="display:grid;gap:4px;font-size:13px;margin-bottom:10px;">
            @foreach($q->options as $opt)
            @php
                $isCorrect = strtoupper($opt['key']) === strtoupper($q->correct_answer ?? '');
                $isChosen = $answer && strtoupper($answer->answer_text ?? '') === strtoupper($opt['key']);
            @endphp
            <div style="padding:4px 10px;border-radius:6px;{{ $isCorrect ? 'background:#DCFCE7;color:#15803D;font-weight:600;' : ($isChosen && !$isCorrect ? 'background:#FEE2E2;color:#DC2626;' : 'color:var(--text-secondary);') }}">
                <strong>{{ $opt['key'] }}.</strong> {{ $opt['text'] }}
                @if($isCorrect) <i class="fas fa-check-circle"></i> @endif
                @if($isChosen) <i class="fas fa-arrow-left" style="font-size:11px;"></i> jawaban santri @endif
            </div>
            @endforeach
        </div>
        @endif

        @if($answer)
            @if($q->question_type === 'essay')
            <div style="background:#F8FAFC;padding:10px;border-radius:6px;margin-bottom:10px;">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Jawaban Santri:</div>
                <p style="font-size:13px;white-space:pre-wrap;">{{ $answer->answer_text ?? '(tidak dijawab)' }}</p>
            </div>
            @if($q->correct_answer)
            <div style="background:#FEF3C7;padding:8px 10px;border-radius:6px;margin-bottom:10px;font-size:12px;">
                <i class="fas fa-key" style="color:#D97706;"></i> <strong>Kunci:</strong> {{ $q->correct_answer }}
            </div>
            @endif
            @endif

            <input type="hidden" name="grades[{{ $q->id }}][answer_id]" value="{{ $answer->id }}">
            <div style="display:flex;align-items:center;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;">Poin diberikan:</label>
                    <input type="number" name="grades[{{ $q->id }}][points_earned]" class="form-input" style="width:80px;"
                           value="{{ $answer->points_earned ?? 0 }}" min="0" max="{{ $q->points }}">
                    <span style="font-size:11px;color:var(--text-muted);">/ {{ $q->points }}</span>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;">Benar?</label>
                    <select name="grades[{{ $q->id }}][is_correct]" class="form-select" style="width:100px;">
                        <option value="1" {{ $answer->is_correct ? 'selected' : '' }}>✅ Ya</option>
                        <option value="0" {{ !$answer->is_correct ? 'selected' : '' }}>❌ Tidak</option>
                    </select>
                </div>
            </div>
        @else
            <p style="font-size:13px;color:var(--text-muted);font-style:italic;">— Tidak dijawab —</p>
        @endif
    </div>
    @endforeach

    <div style="text-align:center;margin-top:20px;">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Simpan Nilai</button>
    </div>
</form>
@endsection
