@extends('layouts.app')

@section('title', 'Dashboard Statistik')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="muted small mb-2">Data Training</div>
                    <div class="metric-value">{{ number_format($stats['training_count']) }}</div>
                    <div class="muted mt-2">Data Historis Mahasiswa</div>
                </div>
                <span class="badge text-bg-primary"><i data-lucide="database"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="muted small mb-2">Algoritma Aktif</div>
                    <div class="metric-value fs-2">{{ $stats['active_algorithm'] }}</div>
                    <div class="muted mt-2">Classification</div>
                </div>
                <span class="badge text-bg-info"><i data-lucide="cpu"></i></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="muted small mb-2">Akurasi Model</div>
                    <div class="metric-value">{{ number_format($stats['accuracy'], 2) }}%</div>
                    <div class="muted mt-2">Cross Validation</div>
                </div>
                <span class="badge text-bg-success"><i data-lucide="activity"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="panel chart-box">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Statistik Akademik</h2>
                <span class="badge text-bg-light border">{{ $stats['history_count'] }} riwayat prediksi</span>
            </div>
            <canvas id="academicChart" height="130"></canvas>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel chart-box">
            <h2 class="h5 fw-bold mb-3">Distribusi Kelulusan</h2>
            <canvas id="graduationChart" height="180"></canvas>
        </div>
    </div>
</div>

<div class="table-panel mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Riwayat Prediksi Terbaru</h2>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('predictions.index') }}">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Algoritma</th>
                <th>Status</th>
                <th>Probabilitas Lulus</th>
                <th>Waktu</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($latestHistories as $history)
                <tr>
                    <td>{{ $history->algorithm }}</td>
                    <td>
                        <span class="status-pill {{ $history->predicted_status === 'Lulus' ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ $history->predicted_status }}
                        </span>
                    </td>
                    <td>{{ number_format($history->probability_lulus, 2) }}%</td>
                    <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center muted py-4">Belum ada riwayat prediksi.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('academicChart'), {
        type: 'bar',
        data: {
            labels: ['Rata-rata IPK', 'Rata-rata Kehadiran', 'Rata-rata SKS'],
            datasets: [{
                label: 'Nilai',
                data: [{{ $stats['avg_ipk'] }}, {{ $stats['avg_kehadiran'] }}, {{ $stats['avg_sks'] }}],
                backgroundColor: ['#0f766e', '#2563eb', '#d97706'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('graduationChart'), {
        type: 'doughnut',
        data: {
            labels: ['Lulus', 'Tidak Lulus'],
            datasets: [{
                data: [{{ $stats['lulus'] }}, {{ $stats['tidak_lulus'] }}],
                backgroundColor: ['#16a34a', '#dc2626']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush
