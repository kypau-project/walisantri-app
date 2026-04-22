@extends('layouts.app')
@section('title', 'Raport')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <a href="/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>📊 Raport</h2>
            <p>Nilai dan raport santri</p>
        </div>
    </div>
</div>

<div class="app-content fade-in">
    @if($reports->count() > 0)
    @foreach($reports as $report)
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Semester {{ $report->semester }}</h3>
                <p class="card-subtitle">Tahun Ajaran {{ $report->academic_year }}</p>
            </div>
            <a href="/reports/{{ $report->id }}/download" class="btn btn-primary btn-sm" id="download-report-{{ $report->id }}">
                <i class="fas fa-download"></i> PDF
            </a>
        </div>

        <!-- Summary -->
        <div style="display:flex;gap:12px;margin-bottom:16px;">
            <div style="flex:1;background:var(--primary-50);border-radius:12px;padding:14px;text-align:center;">
                <div style="font-size:24px;font-weight:800;color:var(--primary);">{{ number_format($report->average_score, 1) }}</div>
                <div style="font-size:11px;color:var(--text-secondary);font-weight:600;">Rata-rata</div>
            </div>
            <div style="flex:1;background:#FEF3C7;border-radius:12px;padding:14px;text-align:center;">
                <div style="font-size:24px;font-weight:800;color:#D97706;">{{ $report->rank ?? '-' }}</div>
                <div style="font-size:11px;color:var(--text-secondary);font-weight:600;">Peringkat</div>
            </div>
        </div>

        <!-- Grades -->
        @if($report->grades)
        <h4 style="font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">Detail Nilai</h4>
        @foreach($report->grades as $grade)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
            <span style="font-size:13px;font-weight:500;">{{ $grade['subject'] }}</span>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:80px;height:6px;background:#E2E8F0;border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $grade['score'] }}%;height:100%;background:{{ $grade['score'] >= 80 ? 'var(--success)' : ($grade['score'] >= 65 ? 'var(--warning)' : 'var(--danger)') }};border-radius:3px;transition:width 0.5s;"></div>
                </div>
                <span style="font-size:14px;font-weight:700;min-width:28px;text-align:right;color:{{ $grade['score'] >= 80 ? 'var(--success)' : ($grade['score'] >= 65 ? 'var(--warning)' : 'var(--danger)') }};">{{ $grade['score'] }}</span>
            </div>
        </div>
        @endforeach
        @endif
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <i class="fas fa-chart-bar"></i>
        <h3>Belum Ada Raport</h3>
        <p>Raport akan muncul setelah dipublikasikan.</p>
    </div>
    @endif
</div>
@endsection
