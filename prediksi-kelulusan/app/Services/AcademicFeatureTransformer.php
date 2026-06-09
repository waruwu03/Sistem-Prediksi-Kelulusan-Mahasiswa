<?php

namespace App\Services;

class AcademicFeatureTransformer
{
    public static function vector(array|object $data): array
    {
        $row = is_array($data) ? $data : $data->toArray();

        return [
            max(0, min(1, (float) $row['ipk'] / 4)),
            max(0, min(1, (int) $row['kehadiran'] / 100)),
            max(0, min(1, (int) $row['sks_lulus'] / 144)),
            self::workValue((string) $row['status_kerja']),
            self::genderValue((string) $row['jenis_kelamin']),
        ];
    }

    public static function label(array|object $data): string
    {
        $row = is_array($data) ? $data : $data->toArray();

        return (string) $row['status_kelulusan'];
    }

    public static function algorithmOptions(): array
    {
        return [
            'naive_bayes' => 'Naive Bayes',
            'knn' => 'KNN',
            'decision_tree' => 'Decision Tree',
            'neural_network' => 'Neural Network',
        ];
    }

    private static function workValue(string $status): float
    {
        return match ($status) {
            'Part Time' => 0.5,
            'Full Time' => 1.0,
            default => 0.0,
        };
    }

    private static function genderValue(string $gender): float
    {
        return $gender === 'Perempuan' ? 1.0 : 0.0;
    }
}
