<?php
namespace App\Http\Controllers;

use App\Services\GetPromptService;
use Inertia\Inertia;

class PromptController extends Controller
{
    public function __construct(
        private readonly GetPromptService $getPromptService
    ){
    }

    public function index()
    {
        return Inertia::render('Prompt/Index', [
            'templatePrompts' => $this->getPromptService->getPromptsOrderedBySlug(),
        ]);
    }
}