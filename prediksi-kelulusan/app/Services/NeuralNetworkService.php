<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Phpml\Classification\MLPClassifier;

class NeuralNetworkService extends BaseClassifier
{
    public function key(): string
    {
        return 'neural_network';
    }

    public function name(): string
    {
        return 'Neural Network';
    }

    private ?Collection $lastTrainingData = null;
    private ?MLPClassifier $trainedClassifier = null;
    private ?array $trainedWeights = null;
    private ?float $trainedBias = null;

    public function predict(Collection $trainingData, array $input): array
    {
        $rows = $this->rows($trainingData);

        if (count($rows) < 2) {
            return $this->emptyResult();
        }

        if ($this->lastTrainingData === $trainingData && $this->trainedClassifier !== null) {
            $classifier = $this->trainedClassifier;
            $weights = $this->trainedWeights;
            $bias = $this->trainedBias;
        } else {
            [$samples, $labels] = $this->samplesAndLabels($rows);
            $classifier = new MLPClassifier(5, [6], [self::POSITIVE, self::NEGATIVE], 24, null, 0.35);
            $classifier->train($samples, $labels);

            $weights = [0.1, 0.1, 0.1, -0.05, 0.02];
            $bias = -0.2;
            $learningRate = 0.08;

            for ($epoch = 0; $epoch < 24; $epoch++) {
                foreach ($rows as $row) {
                    $vector = AcademicFeatureTransformer::vector($row);
                    $target = AcademicFeatureTransformer::label($row) === self::POSITIVE ? 1 : 0;
                    $prediction = $this->sigmoid($this->dot($weights, $vector) + $bias);
                    $error = $target - $prediction;

                    foreach ($weights as $index => $weight) {
                        $weights[$index] = $weight + ($learningRate * $error * $vector[$index]);
                    }

                    $bias += $learningRate * $error;
                }
            }

            $this->lastTrainingData = $trainingData;
            $this->trainedClassifier = $classifier;
            $this->trainedWeights = $weights;
            $this->trainedBias = $bias;
        }

        $predicted = (string) $classifier->predict(AcademicFeatureTransformer::vector($input));
        $probability = $this->sigmoid($this->dot($weights, AcademicFeatureTransformer::vector($input)) + $bias) * 100;

        return $this->formatResult($this->alignProbabilityWithPrediction($probability, $predicted));
    }

    private function dot(array $weights, array $vector): float
    {
        return array_sum(array_map(fn ($weight, $value): float => $weight * $value, $weights, $vector));
    }

    private function sigmoid(float $value): float
    {
        return 1 / (1 + exp(-$value));
    }
}
