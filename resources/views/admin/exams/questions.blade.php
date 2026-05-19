@extends('layouts.admin')
@section('page-title', 'Kelola Soal: ' . $exam->title)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <div>
        <a href="/admin/exams/{{ $exam->id }}" style="color:var(--primary);text-decoration:none;font-size:13px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail Ujian
        </a>
        <h3 style="margin-top:4px;">{{ $exam->title }} — {{ $exam->subject }}</h3>
        <p style="font-size:13px;color:var(--text-secondary);">
            {{ $exam->questions->count() }} soal · Total: {{ $exam->questions->sum('points') }} poin
        </p>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-error" style="margin-bottom:16px;">
    <i class="fas fa-exclamation-circle"></i>
    <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
</div>
@endif

<!-- Existing Questions -->
@foreach($exam->questions as $i => $q)
<div class="admin-card" style="margin-bottom:12px;border-left:4px solid {{ $q->question_type === 'multiple_choice' ? 'var(--primary)' : 'var(--warning)' }};">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                <span style="background:var(--primary-50);color:var(--primary-dark);padding:2px 10px;border-radius:var(--radius-full);font-size:12px;font-weight:700;">
                    #{{ $i + 1 }}
                </span>
                <span class="badge {{ $q->question_type === 'multiple_choice' ? 'badge-info' : 'badge-warning' }}" style="font-size:11px;">
                    {{ $q->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}
                </span>
                <span style="font-size:12px;color:var(--text-muted);">{{ $q->points }} poin</span>
            </div>
            <p style="font-size:14px;line-height:1.6;margin-bottom:8px;">{!! nl2br(e($q->question_text)) !!}</p>

            @if($q->question_type === 'multiple_choice' && $q->options)
            <div style="display:grid;gap:4px;font-size:13px;">
                @foreach($q->options as $opt)
                <div style="padding:4px 10px;border-radius:6px;{{ strtoupper($opt['key']) === strtoupper($q->correct_answer ?? '') ? 'background:#DCFCE7;color:#15803D;font-weight:600;' : 'color:var(--text-secondary);' }}">
                    <strong>{{ $opt['key'] }}.</strong> {{ $opt['text'] }}
                    @if(strtoupper($opt['key']) === strtoupper($q->correct_answer ?? ''))
                    <i class="fas fa-check-circle"></i>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($q->question_type === 'essay' && $q->correct_answer)
            <div style="margin-top:6px;padding:6px 10px;background:#F1F5F9;border-radius:6px;font-size:12px;color:var(--text-secondary);">
                <i class="fas fa-key"></i> Kunci: {{ Str::limit($q->correct_answer, 100) }}
            </div>
            @endif
        </div>
        <div style="display:flex;gap:6px;margin-left:12px;flex-shrink:0;">
            <form method="POST" action="/admin/exams/{{ $exam->id }}/questions/{{ $q->id }}" onsubmit="return confirm('Hapus soal ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add New Question Form -->
<div class="admin-card" style="border:2px dashed var(--primary);background:var(--primary-50);">
    <h4 style="margin-bottom:16px;color:var(--primary-dark);"><i class="fas fa-plus-circle"></i> Tambah Soal Baru</h4>
    <form method="POST" action="/admin/exams/{{ $exam->id }}/questions" id="addQuestionForm">
        @csrf
        <div class="form-group">
            <label class="form-label">Tipe Soal *</label>
            <select name="question_type" id="questionType" class="form-select" onchange="toggleOptions()" style="max-width:220px;">
                <option value="multiple_choice">Pilihan Ganda</option>
                <option value="essay">Essay</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Pertanyaan *</label>
            <textarea name="question_text" class="form-input" rows="3" required placeholder="Tulis pertanyaan di sini..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Poin *</label>
            <input type="number" name="points" class="form-input" value="1" min="1" max="100" style="max-width:120px;">
        </div>

        <!-- MC Options -->
        <div id="mcOptions">
            <label class="form-label">Pilihan Jawaban</label>
            <div style="display:grid;gap:8px;margin-bottom:12px;">
                @foreach(['A', 'B', 'C', 'D', 'E'] as $key)
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-weight:700;width:24px;text-align:center;color:var(--primary-dark);">{{ $key }}.</span>
                    <input type="hidden" name="options[{{ $loop->index }}][key]" value="{{ $key }}">
                    <input type="text" name="options[{{ $loop->index }}][text]" class="form-input" placeholder="Isi pilihan {{ $key }}" style="flex:1;">
                </div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label">Kunci Jawaban (huruf) *</label>
                <select name="correct_answer" class="form-select" style="max-width:120px;" id="correctAnswerSelect">
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $key)
                    <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Essay Answer Key -->
        <div id="essayOptions" style="display:none;">
            <div class="form-group">
                <label class="form-label">Kunci / Referensi Jawaban</label>
                <textarea name="essay_answer" class="form-input" rows="2" placeholder="Tulis kunci jawaban sebagai referensi koreksi (opsional)"></textarea>
                <span style="font-size:11px;color:var(--text-muted);">Digunakan sebagai panduan saat koreksi manual oleh admin.</span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Soal</button>
    </form>
</div>

@if($exam->questions->count() > 0 && $exam->status === 'draft')
<div style="text-align:center;margin-top:20px;">
    <form method="POST" action="/admin/exams/{{ $exam->id }}" style="display:inline;">
        @csrf @method('PUT')
        <input type="hidden" name="title" value="{{ $exam->title }}">
        <input type="hidden" name="subject" value="{{ $exam->subject }}">
        <input type="hidden" name="teacher_name" value="{{ $exam->teacher_name }}">
        <input type="hidden" name="exam_date" value="{{ $exam->exam_date->format('Y-m-d') }}">
        <input type="hidden" name="duration_minutes" value="{{ $exam->duration_minutes }}">
        <input type="hidden" name="status" value="active">
        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Aktifkan ujian ini? Santri akan bisa mengaksesnya.')">
            <i class="fas fa-rocket"></i> Aktifkan Ujian ({{ $exam->questions->count() }} soal)
        </button>
    </form>
</div>
@endif

<script>
function toggleOptions() {
    const type = document.getElementById('questionType').value;
    document.getElementById('mcOptions').style.display = type === 'multiple_choice' ? 'block' : 'none';
    document.getElementById('essayOptions').style.display = type === 'essay' ? 'block' : 'none';
}

// Handle essay correct_answer mapping
document.getElementById('addQuestionForm')?.addEventListener('submit', function() {
    const type = document.getElementById('questionType').value;
    if (type === 'essay') {
        const essayAnswer = this.querySelector('[name="essay_answer"]');
        const correctAnswer = this.querySelector('[name="correct_answer"]');
        if (correctAnswer) correctAnswer.value = essayAnswer?.value || '';
    }
});
</script>
@endsection
