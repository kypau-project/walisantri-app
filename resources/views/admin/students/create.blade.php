@extends('layouts.admin')
@section('page-title', 'Tambah Santri')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Form Tambah Santri Baru</h3>
    <form method="POST" action="/admin/students">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Santri *</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name') }}" id="admin-student-name">
            </div>
            <div class="form-group">
                <label class="form-label">NIS *</label>
                <input type="text" name="nis" class="form-input" required value="{{ old('nis') }}" id="admin-student-nis">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Username (Login) *</label>
                <input type="text" name="username" class="form-input" required value="{{ old('username') }}" id="admin-student-username">
            </div>
            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-input" required id="admin-student-password">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kelas</label>
                <input type="text" name="class" class="form-input" value="{{ old('class') }}" id="admin-student-class">
            </div>
            <div class="form-group">
                <label class="form-label">Kamar</label>
                <input type="text" name="room" class="form-input" value="{{ old('room') }}" id="admin-student-room">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jenis Kelamin *</label>
                <select name="gender" class="form-select" id="admin-student-gender">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">No. HP Ayah</label>
                <input type="tel" name="father_phone" class="form-input" value="{{ old('father_phone') }}" id="admin-student-father-phone">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">No. HP Ibu</label>
            <input type="tel" name="mother_phone" class="form-input" value="{{ old('mother_phone') }}" id="admin-student-mother-phone">
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/admin/students" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endsection
