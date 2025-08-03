<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PromptTestCasesService;
use Illuminate\Http\JsonResponse;

class PromptTestCasesController extends Controller
{
    public function __construct(
        private readonly PromptTestCasesService $promptTestCasesService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->promptTestCasesService->getTestCases());
    }
}
