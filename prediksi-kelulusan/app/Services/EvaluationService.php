<?php

namespace App\Services;

use App\Repositories\MahasiswaRepository;
use Illuminate\Support\Collection;

class EvaluationService
{
    public function __construct(
        private readonly MahasiswaRepository $mahasiswaRepository,
        private readonly PredictionService $predictionService,
    ) {
    }

    public function evaluate(string $algorithm = 'naive_bayes'): array
    {
        $data = $this->mahasiswaRepository->allTrainingData()->values();

        if ($data->count() < 5) {
            return $this->emptyMetrics($algorithm);
        }

        [$training, $testing] = $this->split($data);
        $classifier = $this->predictionService->classifier($algorithm);

        $tp = $tn = $fp = $fn = 0;

        foreach ($testing as $row) {
            $actual = AcademicFeatureTransformer::label($row);
            $result = $classifier->predict($training, $row->toArray());
            $predicted = $result['status'];

            if ($actual === 'Lulus' && $predicted === 'Lulus') {
                $tp++;
            } elseif ($actual === 'Tidak Lulus' && $predicted === 'Tidak Lulus') {
                $tn++;
            } elseif ($actual === 'Tidak Lulus' && $predicted === 'Lulus') {
                $fp++;
            } else {
                $fn++;
            }
        }

        $total = max(1, $tp + $tn + $fp + $fn);
        $precision = $this->safeDivide($tp, $tp + $fp);
        $recall = $this->safeDivide($tp, $tp + $fn);
        $f1 = $this->safeDivide(2 * $precision * $recall, $precision + $recall);

        return [
            'algorithm' => $classifier->name(),
            'accuracy' => round((($tp + $tn) / $total) * 100, 2),
            'precision' => round($precision * 100, 2),
            'recall' => round($recall * 100, 2),
            'f1_score' => round($f1 * 100, 2),
            'confusion_matrix' => [
                'tp' => $tp,
                'tn' => $tn,
                'fp' => $fp,
                'fn' => $fn,
            ],
        ];
    }

    public function algorithmComparison(): array
    {
        $comparison = [];

        foreach (array_keys($this->predictionService->classifiers()) as $algorithm) {
            $metrics = $this->evaluate($algorithm);
            $comparison[] = [
                'key' => $algorithm,
                'algorithm' => $metrics['algorithm'],
                'accuracy' => $metrics['accuracy'],
            ];
        }

        return $comparison;
    }

    private function split(Collection $data): array
    {
        $testing = $data->filter(fn ($row, int $index): bool => $index % 5 === 0)->values();
        $training = $data->filter(fn ($row, int $index): bool => $index % 5 !== 0)->values();

        return [$training, $testing];
    }

    private function safeDivide(float $numerator, float $denominator): float
    {
        return $denominator > 0 ? $numerator / $denominator : 0;
    }

    private function emptyMetrics(string $algorithm): array
    {
        $classifier = $this->predictionService->classifier($algorithm);

        return [
            'algorithm' => $classifier->name(),
            'accuracy' => 0,
            'precision' => 0,
            'recall' => 0,
            'f1_score' => 0,
            'confusion_matrix' => ['tp' => 0, 'tn' => 0, 'fp' => 0, 'fn' => 0],
        ];
    }
}
