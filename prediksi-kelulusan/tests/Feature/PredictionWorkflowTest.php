<?php

namespace Tests\Feature;

use App\Services\PredictionService;
use Database\Seeders\MahasiswaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_prediction_algorithms_return_a_result(): void
    {
        $this->seed(MahasiswaSeeder::class);

        $input = [
            'ipk' => 3.5,
            'kehadiran' => 90,
            'sks_lulus' => 120,
            'status_kerja' => 'Tidak Bekerja',
            'jenis_kelamin' => 'Laki-laki',
            'algorithm' => 'naive_bayes',
        ];

        $results = app(PredictionService::class)->compareAll($input);

        $this->assertCount(4, $results);

        foreach ($results as $result) {
            $this->assertContains($result['status'], ['Lulus', 'Tidak Lulus']);
            $this->assertArrayHasKey('probability_lulus', $result);
            $this->assertArrayHasKey('confidence', $result);
        }
    }
}
