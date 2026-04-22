<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport Santri</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #0D9488; padding-bottom: 20px; }
        .header h1 { color: #0D9488; font-size: 22px; margin: 0 0 4px; }
        .header h2 { font-size: 16px; margin: 0 0 2px; color: #555; }
        .header p { font-size: 11px; color: #888; margin: 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 8px; font-size: 12px; }
        .info-table .label { font-weight: bold; width: 140px; color: #555; }
        .grades-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grades-table th { background: #0D9488; color: white; padding: 10px; text-align: left; font-size: 12px; }
        .grades-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        .grades-table tr:nth-child(even) { background: #f9fefb; }
        .summary { margin-top: 20px; display: flex; }
        .summary-box { display: inline-block; padding: 15px 25px; background: #f0fdfa; border: 2px solid #0D9488; border-radius: 10px; text-align: center; margin-right: 15px; }
        .summary-box .value { font-size: 24px; font-weight: bold; color: #0D9488; }
        .summary-box .label { font-size: 10px; color: #666; text-transform: uppercase; }
        .footer { margin-top: 40px; text-align: right; font-size: 11px; color: #888; }
        .signature { margin-top: 60px; }
        .signature-line { border-bottom: 1px solid #333; width: 200px; display: inline-block; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🕌 PESANTREN UQI</h1>
        <h2>RAPORT SANTRI</h2>
        <p>Semester {{ $report->semester }} — Tahun Ajaran {{ $report->academic_year }}</p>
    </div>

    <table class="info-table">
        <tr><td class="label">Nama Santri</td><td>: {{ $student->name }}</td></tr>
        <tr><td class="label">NIS</td><td>: {{ $student->nis }}</td></tr>
        <tr><td class="label">Kelas</td><td>: {{ $student->class ?? '-' }}</td></tr>
        <tr><td class="label">Kamar</td><td>: {{ $student->room ?? '-' }}</td></tr>
    </table>

    @if($report->grades && count($report->grades) > 0)
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Mata Pelajaran</th>
                <th style="width:80px;text-align:center;">Nilai</th>
                <th style="width:100px;text-align:center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->grades as $i => $grade)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $grade['subject'] }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $grade['score'] }}</td>
                <td style="text-align:center;">
                    @if($grade['score'] >= 85) Sangat Baik
                    @elseif($grade['score'] >= 75) Baik
                    @elseif($grade['score'] >= 65) Cukup
                    @else Perlu Peningkatan
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top:25px;">
        <div class="summary-box">
            <div class="value">{{ number_format($report->average_score, 1) }}</div>
            <div class="label">Rata-rata</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $report->rank ?? '-' }}</div>
            <div class="label">Peringkat</div>
        </div>
    </div>

    <div class="signature">
        <div style="float:right;text-align:center;">
            <p>Mengetahui,</p>
            <p>Kepala Pesantren</p>
            <div class="signature-line"></div>
            <p style="margin-top:4px;">________________</p>
        </div>
        <div style="clear:both;"></div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }} — UQI Smart System</p>
    </div>
</body>
</html>
