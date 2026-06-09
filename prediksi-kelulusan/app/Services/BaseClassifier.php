<?php

namespace App\Services;

use Illuminate\Support\Collection;

abstract class BaseClassifier implements ClassifierInterface
{
    protected const POSITIVE = 'Lulus';

    protected const NEGATIVE = 'Tidak Lulus';

    protected function emptyResult(float $fallback = 50): array
    {
        return $this->formatResult($fallback);
    }

    protected function formatResult(float $lulusProbability): array
    {
        $lulusProbability = max(0, min(100, $lulusProbability));
        $tidakLulusProbability = 100 - $lulusProbability;
        $status = $lulusProbability >= 50 ? self::POSITIVE : self::NEGATIVE;
        $confidence = max($lulusProbability, $tidakLulusProbability);

        return [
            'algorithm_key' => $this->key(),
            'algorithm_name' => $this->name(),
            'status' => $status,
            'display_status' => $status === self::POSITIVE ? 'BERPOTENSI LULUS' : 'BERISIKO TIDAK LULUS',
            'probability_lulus' => round($lulusProbability, 2),
            'probability_tidak_lulus' => round($tidakLulusProbability, 2),
            'confidence' => round($confidence, 2),
            'keterangan' => $this->description($status, $confidence),
        ];
    }

    protected function rows(Collection $trainingData): array
    {
        return $trainingData
            ->filter(fn ($row): bool => in_array(AcademicFeatureTransformer::label($row), [self::POSITIVE, self::NEGATIVE], true))
            ->values()
            ->all();
    }

    protected function samplesAndLabels(array $rows): array
    {
        $samples = [];
        $labels = [];

        foreach ($rows as $row) {
            $samples[] = AcademicFeatureTransformer::vector($row);
            $labels[] = AcademicFeatureTransformer::label($row);
        }

        return [$samples, $labels];
    }

    protected function alignProbabilityWithPrediction(float $lulusProbability, string $predicted): float
    {
        if ($predicted === self::POSITIVE && $lulusProbability < 50) {
            return 100 - $lulusProbability;
        }

        if ($predicted === self::NEGATIVE && $lulusProbability > 50) {
            return 100 - $lulusProbability;
        }

        return $lulusProbability;
    }

    protected function description(string $status, float $confidence): string
    {
        if ($status === self::POSITIVE) {
            return 'Berdasarkan data training dan algoritma yang dipilih, mahasiswa memiliki peluang tinggi untuk menyelesaikan studi hingga semester akhir.';
        }

        if ($confidence >= 75) {
            return 'Model menemukan beberapa indikator akademik yang perlu segera diperbaiki, terutama pada konsistensi kehadiran, capaian SKS, atau IPK.';
        }

        return 'Mahasiswa berada pada area risiko sedang sehingga pendampingan akademik dan pemantauan berkala disarankan.';
    }

    protected function probabilityFromScore(float $score): float
    {
        return 100 / (1 + exp(-$score));
    }
}
