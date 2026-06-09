<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PredictionHistory;
use App\Services\AcademicFeatureTransformer;
use App\Services\EvaluationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly EvaluationService $evaluationService)
    {
    }

    public function index(): View
    {
        $latestHistory = PredictionHistory::latest()->first();
        $activeAlgorithm = $latestHistory?->algorithm ?? 'Naive Bayes';
        $activeKey = array_search($activeAlgorithm, AcademicFeatureTransformer::algorithmOptions(), true) ?: 'naive_bayes';
        $metrics = $this->evaluationService->evaluate($activeKey);

        $stats = [
            'training_count' => Mahasiswa::count(),
            'active_algorithm' => $activeAlgorithm,
            'accuracy' => $metrics['accuracy'],
            'lulus' => Mahasiswa::where('status_kelulusan', 'Lulus')->count(),
            'tidak_lulus' => Mahasiswa::where('status_kelulusan', 'Tidak Lulus')->count(),
            'avg_ipk' => round((float) Mahasiswa::avg('ipk'), 2),
            'avg_kehadiran' => round((float) Mahasiswa::avg('kehadiran'), 2),
            'avg_sks' => round((float) Mahasiswa::avg('sks_lulus'), 2),
            'history_count' => PredictionHistory::count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'latestHistories' => PredictionHistory::latest()->limit(6)->get(),
        ]);
    }
}
