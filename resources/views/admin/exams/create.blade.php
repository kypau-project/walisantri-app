@extends('layouts.admin')
@section('page-title', 'Buat Ujian Baru')

@section('content')
<div class="admin-card" style="max-width:780px;">
    <h3><i class="fas fa-plus-circle" style="color:var(--primary);"></i> Form Buat Ujian Baru</h3>
    <p style="font-size:13px;color:var(--text-secondary);margin-bottom:20px;">
        Setelah mengisi data ujian, Anda akan diarahkan ke halaman penambahan soal.
    </p>

    @if ($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="fas fa-exclamation-circle"></i>
        <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    </div>
    @endif

    <form method="POST" action="/admin/exams">
        @csrf
        <div class="form-group">
            <label class="form-label">Judul Ujian *</label>
            <input type="text" name="title" class="form-input" required placeholder="UTS Al-Quran Kelas 1" value="{{ old('title') }}">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mata Pelajaran *</label>
                <input type="text" name="subject" class="form-input" required placeholder="Al-Quran" value="{{ old('subject') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Ustadz</label>
                <input type="text" name="teacher_name" class="form-input" placeholder="Ustadz Ahmad" value="{{ old('teacher_name') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tanggal Ujian *</label>
                <input type="date" name="exam_date" class="form-input" required value="{{ old('exam_date') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" class="form-input" required min="5" max="300" value="{{ old('duration_minutes', 60) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="start_time" class="form-input" value="{{ old('start_time') }}">
                <span style="font-size:11px;color:var(--text-muted);">Kosongkan = berlaku seharian</span>
            </div>
            <div class="form-group">
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="end_time" class="form-input" value="{{ old('end_time') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi / Instruksi</label>
            <textarea name="description" class="form-input" rows="3" placeholder="Kerjakan dengan jujur. Dilarang membuka buku atau catatan.">{{ old('description') }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex:1;">
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                           style="width:18px;height:18px;accent-color:var(--primary);">
                    Acak urutan soal
                </label>
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="show_result" value="1" {{ old('show_result', '1') ? 'checked' : '' }}
                           style="width:18px;height:18px;accent-color:var(--primary);">
                    Tampilkan hasil ke santri
                </label>
            </div>
        </div>

        <hr style="margin:16px 0;border:none;border-top:1px solid var(--border);">

        <div class="form-group">
            <label class="form-label"><i class="fas fa-users" style="margin-right:6px;color:var(--primary);"></i> Peserta Ujian</label>
            <p style="font-size:12px;color:var(--text-secondary);margin-bottom:8px;">Pilih santri peserta (kosongkan = semua santri aktif)</p>
            <div style="max-height:200px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px;">
                <label style="display:flex;align-items:center;gap:8px;padding:6px 8px;cursor:pointer;border-bottom:1px solid var(--border);font-weight:600;font-size:13px;">
                    <input type="checkbox" id="selectAll" style="width:16px;height:16px;accent-color:var(--primary);">
                    Pilih Semua Santri
                </label>
                @foreach($students as $s)
                <label style="display:flex;align-items:center;gap:8px;padding:5px 8px;cursor:pointer;font-size:13px;">
                    <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="student-checkbox"
                           style="width:16px;height:16px;accent-color:var(--primary);">
                    {{ $s->nis }} — {{ $s->name }} <span style="color:var(--text-muted);">({{ $s->class }})</span>
                </label>
                @endforeach
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <a href="/admin/exams" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-arrow-right"></i> Buat Ujian & Tambah Soal</button>
        </div>
    </form>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
