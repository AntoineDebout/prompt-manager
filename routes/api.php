<?php

use App\Http\Controllers\Api\PromptTestingController;
use App\Http\Controllers\Api\PromptTestCasesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/prompts/test', PromptTestingController::class)->name('api.prompts.test');
Route::get('/prompts/test-cases', [PromptTestCasesController::class, 'index'])->name('api.prompts.test-cases');
