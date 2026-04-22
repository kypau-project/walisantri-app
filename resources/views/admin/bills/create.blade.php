@extends('layouts.admin')
@section('page-title', 'Buat Tagihan')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Form Buat Tagihan Baru</h3>
    <form method="POST" action="/admin/bills">
        @csrf
        <div class="form-group">
            <label class="form-label">Santri *</label>
            <select name="student_id" class="form-select" required id="bill-student-select">
                <option value="">Pilih Santri</option>
                @foreach($students as $s)
                <option value="{{ $s->id }}">{{ $s->nis }} — {{ $s->name }} ({{ $s->class }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Judul Tagihan *</label>
                <input type="text" name="title" class="form-input" required placeholder="SPP Januari 2024" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tipe *</label>
                <select name="type" class="form-select" required>
                    <option value="spp">SPP</option>
                    <option value="daftar_ulang">Daftar Ulang</option>
                    <option value="seragam">Seragam</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jumlah (Rp) *</label>
                <input type="number" name="amount" class="form-input" required min="1000" value="{{ old('amount') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jatuh Tempo</label>
                <input type="date" name="due_date" class="form-input" value="{{ old('due_date') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    <option value="">-</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tahun</label>
                <input type="text" name="year" class="form-input" value="{{ date('Y') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="2" placeholder="Keterangan tagihan">{{ old('description') }}</textarea>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/admin/bills" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Buat Tagihan</button>
        </div>
    </form>
</div>
@endsection
