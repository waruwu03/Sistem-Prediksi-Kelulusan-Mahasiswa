<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Prediksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; font-family: Arial, sans-serif; letter-spacing: 0; }
        .sheet { width: min(860px, calc(100vw - 32px)); margin: 28px auto; background: #fff; border: 1px solid #d9e1eb; border-radius: 8px; padding: 34px; }
        @media print { body { background: #fff; } .sheet { border: 0; margin: 0; width: 100%; } .no-print { display: none; } }
    </style>
</head>
<body>
<main class="sheet">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 fw-bold">Sistem Prediksi Kelulusan Mahasiswa</h1>
            <p class="text-secondary mb-0">Hasil Prediksi Kelulusan</p>
        </div>
        <button class="btn btn-primary no-print" onclick="window.print()">Export PDF</button>
    </div>

    @if($history->nama_mahasiswa)
    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:#f4f7fb; border:1px solid #d9e1eb;">
        <div style="width:48px;height:48px;border-radius:50%;background:#0f766e;display:grid;place-items:center;color:#fff;font-size:1.2rem;font-weight:700;flex-shrink:0;">
            {{ strtoupper(substr($history->nama_mahasiswa, 0, 1)) }}
        </div>
        <div>
            <div class="fw-bold fs-6">{{ $history->nama_mahasiswa }}</div>
            <div class="text-secondary small">NIM: {{ $history->nim }}</div>
        </div>
    </div>
    @endif

    <table class="table table-bordered">
        @if($history->nim)
        <tr><th style="width: 260px;">NIM</th><td>{{ $history->nim }}</td></tr>
        @endif
        @if($history->nama_mahasiswa)
        <tr><th>Nama Mahasiswa</th><td>{{ $history->nama_mahasiswa }}</td></tr>
        @endif
        <tr><th>Algoritma</th><td>{{ $history->algorithm }}</td></tr>
        <tr><th>Status Prediksi</th><td><strong>{{ $history->predicted_status }}</strong></td></tr>
        <tr><th>Probabilitas Lulus</th><td>{{ number_format($history->probability_lulus, 2) }}%</td></tr>
        <tr><th>Probabilitas Tidak Lulus</th><td>{{ number_format($history->probability_tidak_lulus, 2) }}%</td></tr>
        <tr><th>Tingkat Keyakinan</th><td>{{ number_format($history->confidence, 2) }}%</td></tr>
        <tr><th>Waktu Prediksi</th><td>{{ $history->created_at->format('d M Y H:i') }}</td></tr>
    </table>

    <h2 class="h5 fw-bold mt-4">Data Testing</h2>
    <table class="table table-bordered">
        @foreach ($history->input_data as $label => $value)
            <tr><th style="width: 260px;">{{ str_replace('_', ' ', ucfirst($label)) }}</th><td>{{ $value }}</td></tr>
        @endforeach
    </table>

    <h2 class="h5 fw-bold mt-4">Keterangan</h2>
    <p>{{ $history->keterangan }}</p>
</main>
</body>
</html>
