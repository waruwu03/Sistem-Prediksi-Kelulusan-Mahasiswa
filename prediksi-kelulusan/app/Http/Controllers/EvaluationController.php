<?php

namespace App\Http\Controllers;

use App\Services\AcademicFeatureTransformer;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function __construct(private readonly EvaluationService $evaluationService)
    {
    }

    public function index(Request $request): View
    {
        $algorithm = $request->string('algorithm')->toString() ?: 'naive_bayes';

        return view('evaluation.index', [
            'algorithmOptions' => AcademicFeatureTransformer::algorithmOptions(),
            'selectedAlgorithm' => $algorithm,
            'metrics' => $this->evaluationService->evaluate($algorithm),
            'comparison' => $this->evaluationService->algorithmComparison(),
        ]);
    }
}
