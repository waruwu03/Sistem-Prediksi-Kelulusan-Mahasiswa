<?php

namespace App\Http\Controllers;

use App\Http\Requests\PredictionRequest;
use App\Models\PredictionHistory;
use App\Repositories\MahasiswaRepository;
use App\Services\AcademicFeatureTransformer;
use App\Services\EvaluationService;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function __construct(
        private readonly PredictionService $predictionService,
        private readonly EvaluationService $evaluationService,
        private readonly MahasiswaRepository $mahasiswaRepository,
    ) {
    }

    public function index(): View
    {
        return view('predictions.index', [
            'students' => $this->mahasiswaRepository->allStudentsForDropdown(),
            'algorithmOptions' => AcademicFeatureTransformer::algorithmOptions(),
            'comparison' => $this->evaluationService->algorithmComparison(),
            'histories' => PredictionHistory::latest()->paginate(8),
        ]);
    }

    public function predict(PredictionRequest $request): View
    {
        $input = $request->validated();
        $result = $this->predictionService->predict($input);

        return view('predictions.index', [
            'students' => $this->mahasiswaRepository->allStudentsForDropdown(),
            'algorithmOptions' => AcademicFeatureTransformer::algorithmOptions(),
            'input' => $input,
            'result' => $result,
            'comparison' => $this->evaluationService->algorithmComparison(),
            'histories' => PredictionHistory::latest()->paginate(8),
        ]);
    }

    public function compare(PredictionRequest $request): View
    {
        $input = $request->validated();
        $result = $this->predictionService->predict($input);
        $allResults = $this->predictionService->compareAll($input);

        return view('predictions.index', [
            'students' => $this->mahasiswaRepository->allStudentsForDropdown(),
            'algorithmOptions' => AcademicFeatureTransformer::algorithmOptions(),
            'input' => $input,
            'result' => $result,
            'allResults' => $allResults,
            'comparison' => $this->evaluationService->algorithmComparison(),
            'histories' => PredictionHistory::latest()->paginate(8),
        ]);
    }

    public function print(PredictionHistory $history): View
    {
        return view('predictions.print', compact('history'));
    }
}
