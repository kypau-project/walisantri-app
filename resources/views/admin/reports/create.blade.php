@extends('layouts.admin')
@section('page-title', 'Buat Raport')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Form Buat Raport</h3>
    <form method="POST" action="/admin/reports">
        @csrf
        <div class="form-group">
            <label class="form-label">Santri *</label>
            <select name="student_id" class="form-select" required>
                <option value="">Pilih Santri</option>
                @foreach($students as $s)
                <option value="{{ $s->id }}">{{ $s->nis }} — {{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Semester *</label>
                <select name="semester" class="form-select" required>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tahun Ajaran *</label>
                <input type="text" name="academic_year" class="form-input" required value="{{ date('Y') }}/{{ date('Y')+1 }}">
            </div>
        </div>

        <h4 style="font-size:14px;font-weight:700;margin:20px 0 12px;color:var(--primary-dark);">Nilai Mata Pelajaran</h4>
        <div id="gradeFields">
            @foreach(['Al-Quran', 'Hadits', 'Fiqih', 'Aqidah', 'Bahasa Arab', 'Matematika', 'IPA', 'Bahasa Indonesia', 'Bahasa Inggris'] as $i => $subject)
            <div class="form-row" style="margin-bottom:8px;">
                <div class="form-group" style="margin-bottom:0;">
                    <input type="text" name="grades[{{ $i }}][subject]" class="form-input" value="{{ $subject }}">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <input type="number" name="grades[{{ $i }}][score]" class="form-input" placeholder="Nilai" min="0" max="100">
                </div>
            </div>
            @endforeach
        </div>

        <div style="display:flex;gap:10px;margin-top:20px;">
            <a href="/admin/reports" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Raport</button>
        </div>
    </form>
</div>
@endsection
