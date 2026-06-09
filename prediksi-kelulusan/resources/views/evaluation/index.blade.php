@extends('layouts.app')

@section('title', 'Evaluasi Model')

@section('content')
<div class="panel mb-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <h2 class="h5 fw-bold mb-1">Evaluasi Model</h2>
            <div class="muted">Accuracy, precision, recall, F1 score, dan confusion matrix</div>
        </div>
        <form class="d-flex gap-2" method="get" action="{{ route('evaluation.index') }}">
            <select class="form-select" name="algorithm">
                @foreach ($algorithmOptions as $key => $name)
                    <option value="{{ $key }}" @selected($selectedAlgorithm === $key)>{{ $name }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="submit">
                <i data-lucide="refresh-cw"></i> Evaluasi
            </button>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach ([
        'Accuracy' => $metrics['accuracy'],
        'Precision' => $metrics['precision'],
        'Recall' => $metrics['recall'],
        'F1 Score' => $metrics['f1_score'],
    ] as $label => $value)
        <div class="col-md-3">
            <div class="metric-card">
                <div class="muted small mb-2">{{ $label }}</div>
                <div class="metric-value">{{ number_format($value, 2) }}%</div>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar" style="width: {{ $value }}%"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="panel chart-box">
            <h2 class="h5 fw-bold mb-3">Confusion Matrix</h2>
            <canvas id="matrixChart" height="180"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel chart-box">
            <h2 class="h5 fw-bold mb-3">Perbandingan Akurasi</h2>
            <canvas id="accuracyChart" height="180"></canvas>
        </div>
    </div>
</div>

<div class="table-panel mt-3">
    <h2 class="h5 fw-bold mb-3">Ringkasan Perbandingan</h2>
    <table class="table align-middle">
        <thead>
        <tr>
            <th>Algoritma</th>
            <th>Akurasi</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($comparison as $row)
            <tr>
                <td>{{ $row['algorithm'] }}</td>
                <td>{{ number_format($row['accuracy'], 2) }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('matrixChart'), {
        type: 'bar',
        data: {
            labels: ['TP', 'TN', 'FP', 'FN'],
            datasets: [{
                label: '{{ $metrics['algorithm'] }}',
                data: [
                    {{ $metrics['confusion_matrix']['tp'] }},
                    {{ $metrics['confusion_matrix']['tn'] }},
                    {{ $metrics['confusion_matrix']['fp'] }},
                    {{ $metrics['confusion_matrix']['fn'] }}
                ],
                backgroundColor: ['#16a34a', '#2563eb', '#d97706', '#dc2626'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('accuracyChart'), {
        type: 'line',
        data: {
            labels: @json(array_column($comparison, 'algorithm')),
            datasets: [{
                label: 'Akurasi',
                data: @json(array_column($comparison, 'accuracy')),
                borderColor: '#0f766e',
                backgroundColor: 'rgba(15, 118, 110, .15)',
                fill: true,
                tension: .35
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
</script>
@endpush
