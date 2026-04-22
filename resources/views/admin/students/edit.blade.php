@extends('layouts.admin')
@section('page-title', 'Edit Santri')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Edit Data Santri: {{ $student->name }}</h3>
    <form method="POST" action="/admin/students/{{ $student->id }}">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Santri *</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name', $student->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">NIS</label>
                <input type="text" class="form-input" value="{{ $student->nis }}" disabled style="background:#f1f5f9;">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kelas</label>
                <input type="text" name="class" class="form-input" value="{{ old('class', $student->class) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Kamar</label>
                <input type="text" name="room" class="form-input" value="{{ old('room', $student->room) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jenis Kelamin *</label>
                <select name="gender" class="form-select">
                    <option value="L" {{ $student->gender === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $student->gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $student->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="alumni" {{ $student->status === 'alumni' ? 'selected' : '' }}>Alumni</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">No. HP Ayah</label>
                <input type="tel" name="father_phone" class="form-input" value="{{ old('father_phone', $student->father_phone) }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. HP Ibu</label>
                <input type="tel" name="mother_phone" class="form-input" value="{{ old('mother_phone', $student->mother_phone) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-input" rows="3">{{ old('address', $student->address) }}</textarea>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/admin/students" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        </div>
    </form>
</div>
@endsection
