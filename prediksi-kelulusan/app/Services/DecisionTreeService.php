<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Phpml\Classification\DecisionTree;

class DecisionTreeService extends BaseClassifier
{
    public function key(): string
    {
        return 'decision_tree';
    }

    public function name(): string
    {
        return 'Decision Tree';
    }

    private ?Collection $lastTrainingData = null;
    private ?DecisionTree $trainedClassifier = null;
    private ?float $ipkThreshold = null;
    private ?float $attendanceThreshold = null;
    private ?float $sksThreshold = null;

    public function predict(Collection $trainingData, array $input): array
    {
        $rows = collect($this->rows($trainingData));

        if ($rows->isEmpty()) {
            return $this->emptyResult();
        }

        if ($this->lastTrainingData === $trainingData && $this->trainedClassifier !== null) {
            $classifier = $this->trainedClassifier;
            $ipkThreshold = $this->ipkThreshold;
            $attendanceThreshold = $this->attendanceThreshold;
            $sksThreshold = $this->sksThreshold;
        } else {
            [$samples, $labels] = $this->samplesAndLabels($rows->all());
            $classifier = new DecisionTree();
            $classifier->train($samples, $labels);

            $positiveRows = $rows->filter(fn ($row): bool => AcademicFeatureTransformer::label($row) === self::POSITIVE);
            $negativeRows = $rows->filter(fn ($row): bool => AcademicFeatureTransformer::label($row) === self::NEGATIVE);

            $ipkThreshold = $this->middleThreshold($positiveRows->avg('ipk'), $negativeRows->avg('ipk'), 3.0);
            $attendanceThreshold = $this->middleThreshold($positiveRows->avg('kehadiran'), $negativeRows->avg('kehadiran'), 75);
            $sksThreshold = $this->middleThreshold($positiveRows->avg('sks_lulus'), $negativeRows->avg('sks_lulus'), 110);

            $this->lastTrainingData = $trainingData;
            $this->trainedClassifier = $classifier;
            $this->ipkThreshold = $ipkThreshold;
            $this->attendanceThreshold = $attendanceThreshold;
            $this->sksThreshold = $sksThreshold;
        }

        $predicted = (string) $classifier->predict(AcademicFeatureTransformer::vector($input));

        $score = 0;
        $score += (float) $input['ipk'] >= $ipkThreshold ? 2.4 : -2.2;
        $score += (int) $input['kehadiran'] >= $attendanceThreshold ? 1.8 : -1.5;
        $score += (int) $input['sks_lulus'] >= $sksThreshold ? 1.7 : -1.4;
        $score += $input['status_kerja'] === 'Full Time' ? -1.2 : 0.8;
        $score += $input['status_kerja'] === 'Part Time' ? -0.25 : 0;

        $probability = $this->probabilityFromScore($score / 1.7);

        return $this->formatResult($this->alignProbabilityWithPrediction($probability, $predicted));
    }

    private function middleThreshold(?float $positiveAvg, ?float $negativeAvg, float $fallback): float
    {
        if ($positiveAvg === null || $negativeAvg === null) {
            return $fallback;
        }

        return ($positiveAvg + $negativeAvg) / 2;
    }
}
