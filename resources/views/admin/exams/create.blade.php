@extends('layouts.admin')
@section('page-title', 'Buat Ujian')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Form Buat Ujian Baru</h3>
    <form method="POST" action="/admin/exams">
        @csrf
        <div class="form-group">
            <label class="form-label">Judul Ujian *</label>
            <input type="text" name="title" class="form-input" required placeholder="UTS Matematika" value="{{ old('title') }}">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <input type="text" name="subject" class="form-input" required value="{{ old('subject') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Ujian *</label>
                <input type="date" name="exam_date" class="form-input" required value="{{ old('exam_date') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" class="form-input" required min="10" value="{{ old('duration_minutes', 90) }}">
            </div>
            <div class="form-group">
                <label class="form-label">URL Ujian</label>
                <input type="url" name="exam_url" class="form-input" placeholder="https://exam.uqi.ac.id/start" value="{{ old('exam_url') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="2">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Peserta (kosongkan untuk semua santri)</label>
            <select name="student_ids[]" class="form-select" multiple style="height:120px;">
                @foreach($students as $s)
                <option value="{{ $s->id }}">{{ $s->nis }} — {{ $s->name }}</option>
                @endforeach
            </select>
            <span style="font-size:11px;color:var(--text-secondary);">Tahan Ctrl untuk memilih beberapa santri</span>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/admin/exams" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Buat Ujian</button>
        </div>
    </form>
</div>
@endsection
