<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Phpml\Classification\KNearestNeighbors;

class KNNService extends BaseClassifier
{
    public function key(): string
    {
        return 'knn';
    }

    public function name(): string
    {
        return 'KNN';
    }

    private ?Collection $lastTrainingData = null;
    private ?KNearestNeighbors $trainedClassifier = null;
    private ?int $k = null;

    public function predict(Collection $trainingData, array $input): array
    {
        $rows = $this->rows($trainingData);

        if ($rows === []) {
            return $this->emptyResult();
        }

        $inputVector = AcademicFeatureTransformer::vector($input);

        if ($this->lastTrainingData === $trainingData && $this->trainedClassifier !== null) {
            $classifier = $this->trainedClassifier;
            $k = $this->k;
        } else {
            [$samples, $labels] = $this->samplesAndLabels($rows);
            $k = min(9, count($rows));
            $classifier = new KNearestNeighbors($k);
            $classifier->train($samples, $labels);

            $this->lastTrainingData = $trainingData;
            $this->trainedClassifier = $classifier;
            $this->k = $k;
        }

        $predicted = (string) $classifier->predict($inputVector);
        $distances = [];

        foreach ($rows as $row) {
            $vector = AcademicFeatureTransformer::vector($row);
            $sum = 0;

            foreach ($inputVector as $index => $value) {
                $sum += ($value - $vector[$index]) ** 2;
            }

            $distances[] = [
                'distance' => sqrt($sum),
                'label' => AcademicFeatureTransformer::label($row),
            ];
        }

        usort($distances, fn ($a, $b): int => $a['distance'] <=> $b['distance']);

        $votes = [
            self::POSITIVE => 0.0,
            self::NEGATIVE => 0.0,
        ];

        foreach (array_slice($distances, 0, $k) as $neighbor) {
            $votes[$neighbor['label']] += 1 / ($neighbor['distance'] + 0.001);
        }

        $probability = ($votes[self::POSITIVE] / array_sum($votes)) * 100;

        return $this->formatResult($this->alignProbabilityWithPrediction($probability, $predicted));
    }
}
