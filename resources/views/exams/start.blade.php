@extends('layouts.app')
@section('title', 'Mulai Ujian')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/exams" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Mulai Ujian</h2>
            <p>Konfirmasi sebelum memulai</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    <div class="card" style="text-align:center;padding:30px 20px;">
        <div style="width:80px;height:80px;background:var(--primary-50);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px;">
            📝
        </div>
        <h3 style="font-weight:800;margin-bottom:8px;">{{ $exam->title }}</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">{{ $exam->subject }}
            @if($exam->teacher_name) · {{ $exam->teacher_name }} @endif
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;text-align:center;">
            <div style="padding:12px;background:#F8FAFC;border-radius:var(--radius-sm);">
                <div style="font-size:24px;font-weight:800;color:var(--primary-dark);">{{ $exam->questions->count() }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Soal</div>
            </div>
            <div style="padding:12px;background:#F8FAFC;border-radius:var(--radius-sm);">
                <div style="font-size:24px;font-weight:800;color:var(--primary-dark);">{{ $exam->duration_minutes }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Menit</div>
            </div>
        </div>

        @if($exam->description)
        <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:var(--radius-sm);padding:12px;margin-bottom:20px;text-align:left;font-size:13px;color:#92400E;">
            <strong><i class="fas fa-exclamation-triangle"></i> Instruksi:</strong><br>
            {{ $exam->description }}
        </div>
        @endif

        <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:var(--radius-sm);padding:12px;margin-bottom:20px;text-align:left;font-size:12px;color:#991B1B;">
            <strong>⚠️ Perhatian:</strong>
            <ul style="margin:6px 0 0 16px;line-height:1.8;">
                <li>Setelah memulai, waktu akan berjalan dan <strong>tidak bisa dihentikan</strong></li>
                <li>Jawaban tersimpan otomatis setiap kali Anda berpindah soal</li>
                <li>Jangan berpindah tab/jendela — aktivitas akan tercatat</li>
                <li>Ujian akan otomatis dikumpulkan jika waktu habis</li>
            </ul>
        </div>

        <form method="POST" action="/exams/{{ $exam->id }}/begin" id="beginExamForm">
            @csrf
            <button type="button" class="btn btn-primary btn-lg btn-block" id="startExamBtn">
                <i class="fas fa-play-circle"></i> Mulai Mengerjakan
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('startExamBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Mulai Ujian Sekarang?',
        html: '<p style="color:#64748B;font-size:14px;">Waktu akan langsung berjalan setelah Anda memulai dan <strong>tidak bisa dihentikan</strong>.</p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0D9488',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: '<i class="fas fa-play"></i> Ya, Mulai!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mempersiapkan Ujian...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });
            document.getElementById('beginExamForm').submit();
        }
    });
});
</script>
@endsection
