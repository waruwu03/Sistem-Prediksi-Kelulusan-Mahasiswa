<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Phpml\Classification\NaiveBayes;

class NaiveBayesService extends BaseClassifier
{
    public function key(): string
    {
        return 'naive_bayes';
    }

    public function name(): string
    {
        return 'Naive Bayes';
    }

    private ?Collection $lastTrainingData = null;
    private ?NaiveBayes $trainedClassifier = null;
    private ?array $grouped = null;
    private ?int $totalRows = null;

    public function predict(Collection $trainingData, array $input): array
    {
        $rows = $this->rows($trainingData);

        if (count($rows) < 2) {
            return $this->emptyResult();
        }

        $inputVector = AcademicFeatureTransformer::vector($input);

        if ($this->lastTrainingData === $trainingData && $this->trainedClassifier !== null) {
            $classifier = $this->trainedClassifier;
            $grouped = $this->grouped;
            $totalRows = $this->totalRows;
        } else {
            [$samples, $labels] = $this->samplesAndLabels($rows);
            $classifier = new NaiveBayes();
            $classifier->train($samples, $labels);

            $grouped = [
                self::POSITIVE => [],
                self::NEGATIVE => [],
            ];

            foreach ($rows as $row) {
                $grouped[AcademicFeatureTransformer::label($row)][] = AcademicFeatureTransformer::vector($row);
            }

            $totalRows = count($rows);

            $this->lastTrainingData = $trainingData;
            $this->trainedClassifier = $classifier;
            $this->grouped = $grouped;
            $this->totalRows = $totalRows;
        }

        $predicted = (string) $classifier->predict($inputVector);
        $logScores = [];

        foreach ($grouped as $label => $vectors) {
            $count = max(1, count($vectors));
            $logScore = log($count / $totalRows);

            for ($index = 0; $index < count($inputVector); $index++) {
                $values = array_column($vectors, $index);
                $mean = array_sum($values) / max(1, count($values));
                $variance = array_sum(array_map(fn ($value): float => ($value - $mean) ** 2, $values)) / max(1, count($values));
                $variance = max($variance, 0.0001);
                $logScore += -0.5 * log(2 * pi() * $variance) - (($inputVector[$index] - $mean) ** 2 / (2 * $variance));
            }

            $logScores[$label] = $logScore;
        }

        $maxLog = max($logScores);
        $positive = exp($logScores[self::POSITIVE] - $maxLog);
        $negative = exp($logScores[self::NEGATIVE] - $maxLog);

        $probability = ($positive / ($positive + $negative)) * 100;

        return $this->formatResult($this->alignProbabilityWithPrediction($probability, $predicted));
    }
}
