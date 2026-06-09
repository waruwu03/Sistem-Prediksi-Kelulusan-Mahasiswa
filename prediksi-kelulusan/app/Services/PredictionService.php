<?php

namespace App\Services;

use App\Models\PredictionHistory;
use App\Repositories\MahasiswaRepository;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PredictionService
{
    public function __construct(
        private readonly MahasiswaRepository $mahasiswaRepository,
        private readonly NaiveBayesService $naiveBayesService,
        private readonly KNNService $knnService,
        private readonly DecisionTreeService $decisionTreeService,
        private readonly NeuralNetworkService $neuralNetworkService,
    ) {
    }

    public function classifiers(): array
    {
        return [
            $this->naiveBayesService->key() => $this->naiveBayesService,
            $this->knnService->key() => $this->knnService,
            $this->decisionTreeService->key() => $this->decisionTreeService,
            $this->neuralNetworkService->key() => $this->neuralNetworkService,
        ];
    }

    public function predict(array $input, bool $storeHistory = true): array
    {
        $algorithm = $input['algorithm'] ?? 'naive_bayes';
        $classifier = $this->classifier($algorithm);
        $payload = $this->predictionPayload($input);
        $result = $classifier->predict($this->mahasiswaRepository->allTrainingData(), $payload);

        if ($storeHistory) {
            PredictionHistory::create([
                'user_id' => Auth::id(),
                'nim' => $input['nim'] ?? null,
                'nama_mahasiswa' => $input['nama_mahasiswa'] ?? null,
                'algorithm' => $result['algorithm_name'],
                'input_data' => $payload,
                'predicted_status' => $result['status'],
                'probability_lulus' => $result['probability_lulus'],
                'probability_tidak_lulus' => $result['probability_tidak_lulus'],
                'confidence' => $result['confidence'],
                'keterangan' => $result['keterangan'],
            ]);
        }

        return $result;
    }

    public function compareAll(array $input): array
    {
        $results = [];

        foreach (array_keys($this->classifiers()) as $algorithm) {
            $input['algorithm'] = $algorithm;
            $results[] = $this->predict($input, false);
        }

        return $results;
    }

    public function classifier(string $algorithm): ClassifierInterface
    {
        return $this->classifiers()[$algorithm]
            ?? throw new InvalidArgumentException("Algoritma {$algorithm} tidak tersedia.");
    }

    private function predictionPayload(array $input): array
    {
        return [
            'ipk' => (float) $input['ipk'],
            'kehadiran' => (int) $input['kehadiran'],
            'sks_lulus' => (int) $input['sks_lulus'],
            'status_kerja' => $input['status_kerja'],
            'jenis_kelamin' => $input['jenis_kelamin'],
        ];
    }
}
