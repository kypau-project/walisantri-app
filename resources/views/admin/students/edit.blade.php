@extends('layouts.admin')
@section('page-title', 'Edit Santri')

@section('content')
<div class="admin-card" style="max-width:750px;">
    <h3>Edit Data Santri: {{ $student->name }}</h3>

    @if ($student->isClaimed())
    <div style="background:#DCFCE7;border:1px solid #86EFAC;border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;font-size:12px;color:#15803D;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-check-circle"></i>
        <span>Sudah terhubung dengan akun wali: <strong>{{ $student->user->phone ?? '-' }}</strong></span>
    </div>
    @else
    <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;font-size:12px;color:#A16207;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-clock"></i>
        <span>Belum diklaim oleh wali santri</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="/admin/students/{{ $student->id }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Foto Profil --}}
        <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label">Pass Foto Santri</label>
            <div style="display:flex;align-items:center;gap:16px;">
                <div id="photoPreview" style="width:100px;height:130px;border-radius:var(--radius-sm);background:#F1F5F9;display:flex;align-items:center;justify-content:center;overflow:hidden;border:2px dashed var(--border);">
                    @if ($student->photo && Storage::disk('public')->exists($student->photo))
                        <img src="{{ asset('storage/' . $student->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <i class="fas fa-user" style="font-size:32px;color:var(--text-muted);"></i>
                    @endif
                </div>
                <div>
                    <input type="file" name="photo" accept="image/jpeg,image/png" id="photoInput"
                           style="display:none;" onchange="previewPhoto(this)">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-camera"></i> {{ $student->photo ? 'Ganti Foto' : 'Upload Foto' }}
                    </button>
                    <p style="font-size:11px;color:var(--text-muted);margin-top:6px;">JPG/PNG, maks 2MB, ukuran 3x4</p>
                    @if ($student->photo)
                    <p style="font-size:11px;color:var(--success);margin-top:2px;"><i class="fas fa-check"></i> Foto tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Santri *</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name', $student->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Kelamin *</label>
                <select name="gender" class="form-select">
                    <option value="L" {{ $student->gender === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $student->gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">No. Induk</label>
                <input type="text" class="form-input" value="{{ $student->nis }}" disabled style="background:#f1f5f9;">
            </div>
            <div class="form-group">
                <label class="form-label">NISN</label>
                <input type="text" name="nisn" class="form-input" value="{{ old('nisn', $student->nisn) }}"
                       placeholder="Nomor Induk Siswa Nasional">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kelas</label>
                <input type="text" name="class" class="form-input" value="{{ old('class', $student->class) }}"
                       placeholder="Contoh: 1C-PI">
            </div>
            <div class="form-group">
                <label class="form-label">Kamar</label>
                <input type="text" name="room" class="form-input" value="{{ old('room', $student->room) }}"
                       placeholder="Contoh: D7">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tahun Masuk</label>
                <input type="text" name="enrollment_year" class="form-input" value="{{ old('enrollment_year', $student->enrollment_year) }}"
                       placeholder="Contoh: 2024-2025">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="form-input"
                       value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $student->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="alumni" {{ $student->status === 'alumni' ? 'selected' : '' }}>Alumni</option>
            </select>
        </div>

        <hr style="margin:16px 0;border:none;border-top:1px solid var(--border);">
        <h4 style="font-size:14px;font-weight:700;color:var(--primary-dark);margin-bottom:12px;">
            <i class="fas fa-users" style="margin-right:6px;"></i> Data Orang Tua
        </h4>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Ayah</label>
                <input type="text" name="father_name" class="form-input" value="{{ old('father_name', $student->father_name) }}"
                       placeholder="Nama lengkap ayah">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Ibu</label>
                <input type="text" name="mother_name" class="form-input" value="{{ old('mother_name', $student->mother_name) }}"
                       placeholder="Nama lengkap ibu">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">No. WA Ayah</label>
                <input type="tel" name="father_phone" class="form-input" value="{{ old('father_phone', $student->father_phone) }}"
                       placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label class="form-label">No. WA Ibu</label>
                <input type="tel" name="mother_phone" class="form-input" value="{{ old('mother_phone', $student->mother_phone) }}"
                       placeholder="08xxxxxxxxxx">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-input" rows="3">{{ old('address', $student->address) }}</textarea>
        </div>

        <div style="display:flex;gap:10px;margin-top:12px;">
            <a href="/admin/students" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        </div>
    </form>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
