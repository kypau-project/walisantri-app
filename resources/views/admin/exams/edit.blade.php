@extends('layouts.admin')
@section('page-title', 'Edit Ujian')

@section('content')
<div class="admin-card" style="max-width:780px;">
    <h3><i class="fas fa-edit" style="color:var(--primary);"></i> Edit Ujian: {{ $exam->title }}</h3>

    @if ($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="fas fa-exclamation-circle"></i>
        <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    </div>
    @endif

    <form method="POST" action="/admin/exams/{{ $exam->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Judul Ujian *</label>
            <input type="text" name="title" class="form-input" required value="{{ old('title', $exam->title) }}">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <input type="text" name="subject" class="form-input" required value="{{ old('subject', $exam->subject) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Ustadz</label>
                <input type="text" name="teacher_name" class="form-input" value="{{ old('teacher_name', $exam->teacher_name) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tanggal Ujian *</label>
                <input type="date" name="exam_date" class="form-input" required value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" class="form-input" required min="5" value="{{ old('duration_minutes', $exam->duration_minutes) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="start_time" class="form-input" value="{{ old('start_time', $exam->start_time) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="end_time" class="form-input" value="{{ old('end_time', $exam->end_time) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="3">{{ old('description', $exam->description) }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex:1;">
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ $exam->shuffle_questions ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                    Acak urutan soal
                </label>
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="show_result" value="1" {{ $exam->show_result ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                    Tampilkan hasil ke santri
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" style="max-width:200px;">
                <option value="draft" {{ $exam->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="active" {{ $exam->status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="completed" {{ $exam->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="archived" {{ $exam->status === 'archived' ? 'selected' : '' }}>Arsip</option>
            </select>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <a href="/admin/exams/{{ $exam->id }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
