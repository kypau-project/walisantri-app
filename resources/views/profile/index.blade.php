@extends('layouts.app')
@section('title', 'Profil Santri')

@section('styles')
<style>
.profile-photo-section {
    text-align: center;
    padding: 24px 20px 20px;
}

.profile-photo {
    width: 120px;
    height: 155px;
    border-radius: 16px;
    overflow: hidden;
    background: #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    border: 3px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-photo .photo-fallback {
    font-size: 48px;
    font-weight: 800;
    color: var(--primary);
}

.edit-modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.edit-modal-overlay.active { display: flex; }

.edit-modal {
    background: white;
    border-radius: 20px;
    padding: 28px 24px;
    width: 100%;
    max-width: 420px;
    animation: slideUp 0.3s ease;
}

.edit-modal h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
}

.edit-modal .btn-row {
    display: flex;
    gap: 10px;
    margin-top: 16px;
}

.profile-info-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-50);
    color: var(--primary-dark);
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Profil Santri</h2>
            <p>Informasi lengkap santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($student)
    <!-- Student Photo + Name -->
    <div class="card profile-photo-section">
        <div class="profile-photo">
            @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                <span class="photo-fallback" style="display:none;">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            @else
                <span class="photo-fallback">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            @endif
        </div>
        <h3 style="font-size:20px;font-weight:800;margin-bottom:4px;">{{ $student->name }}</h3>
        <p style="color:var(--text-secondary);font-size:13px;margin-bottom:10px;">No. Induk: {{ $student->nis }}</p>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
            <span class="badge badge-success"><i class="fas fa-check"></i> {{ ucfirst($student->status) }}</span>
            <span class="badge badge-info"><i class="fas fa-venus-mars"></i> {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
            @if($student->enrollment_year)
            <span class="profile-info-badge"><i class="fas fa-calendar"></i> TA {{ $student->enrollment_year }}</span>
            @endif
        </div>
    </div>

    <!-- Data Santri (readonly) -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:8px;"><i class="fas fa-id-card" style="color:var(--primary);margin-right:8px;"></i> Data Santri</h3>

        <div class="profile-item">
            <span class="label">Nama Lengkap</span>
            <span class="value">{{ $student->name }}</span>
        </div>
        <div class="profile-item">
            <span class="label">No. Induk</span>
            <span class="value">{{ $student->nis }}</span>
        </div>
        @if($student->nisn)
        <div class="profile-item">
            <span class="label">NISN</span>
            <span class="value">{{ $student->nisn }}</span>
        </div>
        @endif
        <div class="profile-item">
            <span class="label">Kelas</span>
            <span class="value">{{ $student->class ?? '-' }}</span>
        </div>
        <div class="profile-item">
            <span class="label">Kamar</span>
            <span class="value">{{ $student->room ?? '-' }}</span>
        </div>
        @if($student->enrollment_year)
        <div class="profile-item">
            <span class="label">Tahun Masuk</span>
            <span class="value">{{ $student->enrollment_year }}</span>
        </div>
        @endif
        <div class="profile-item">
            <span class="label">Tanggal Lahir</span>
            <span class="value">{{ $student->birth_date ? $student->birth_date->format('d F Y') : '-' }}</span>
        </div>
    </div>

    <!-- Data Orang Tua + Kontak (editable) -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <h3 class="card-title" style="margin:0;"><i class="fas fa-users" style="color:var(--primary);margin-right:8px;"></i> Data Orang Tua</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="openEditModal()" id="btn-edit-contact">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>

        @if($student->father_name)
        <div class="profile-item">
            <span class="label">Nama Ayah</span>
            <span class="value">{{ $student->father_name }}</span>
        </div>
        @endif
        <div class="profile-item">
            <span class="label">No. WA Ayah</span>
            <span class="value">
                @if($student->father_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $student->father_phone) }}" target="_blank"
                   style="color:var(--success);text-decoration:none;">
                    <i class="fab fa-whatsapp"></i> {{ $student->father_phone }}
                </a>
                @else - @endif
            </span>
        </div>
        @if($student->mother_name)
        <div class="profile-item">
            <span class="label">Nama Ibu</span>
            <span class="value">{{ $student->mother_name }}</span>
        </div>
        @endif
        <div class="profile-item">
            <span class="label">No. WA Ibu</span>
            <span class="value">
                @if($student->mother_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $student->mother_phone) }}" target="_blank"
                   style="color:var(--success);text-decoration:none;">
                    <i class="fab fa-whatsapp"></i> {{ $student->mother_phone }}
                </a>
                @else - @endif
            </span>
        </div>
        <div class="profile-item">
            <span class="label">Alamat</span>
            <span class="value" style="max-width:60%;">{{ $student->address ?? '-' }}</span>
        </div>
    </div>

    <!-- QR Code -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom:16px;"><i class="fas fa-qrcode" style="color:var(--primary);margin-right:8px;"></i> Barcode ID Santri</h3>
        <div class="qr-container">
            <div style="margin-bottom:12px;">
                {!! QrCode::size(180)->generate($student->barcode_id ?? $student->nis) !!}
            </div>
            <p style="font-size:16px;font-weight:700;color:var(--text);letter-spacing:2px;">{{ $student->barcode_id ?? $student->nis }}</p>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">Tunjukkan QR code ini untuk identifikasi</p>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="edit-modal-overlay" id="editModal">
        <div class="edit-modal">
            <h3><i class="fas fa-edit" style="color:var(--primary);margin-right:8px;"></i> Edit Kontak & Alamat</h3>
            <form method="POST" action="/profile/update" id="editContactForm">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">No. WA Ayah</label>
                    <input type="tel" name="father_phone" class="form-input" value="{{ $student->father_phone }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">No. WA Ibu</label>
                    <input type="tel" name="mother_phone" class="form-input" value="{{ $student->mother_phone }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-input" rows="3" placeholder="Alamat lengkap">{{ $student->address }}</textarea>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="flex:1;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @else
    <div class="empty-state">
        <i class="fas fa-user-slash"></i>
        <h3>Data Santri Belum Ada</h3>
        <p>Silakan hubungi admin untuk mendaftarkan santri.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function openEditModal() {
    document.getElementById('editModal').classList.add('active');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}
// Close on overlay click
document.getElementById('editModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection
