<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface ClassifierInterface
{
    public function key(): string;

    public function name(): string;

    public function predict(Collection $trainingData, array $input): array;
}
