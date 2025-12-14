<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicPortfolioController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('json.accepts')->post('login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'json.accepts'])->post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('json.accepts')->get('/public/portfolio', [PublicPortfolioController::class, 'index']);

Route::middleware(['auth:sanctum', 'json.accepts'])->group(function (): void {
    Route::post('/profile/update', [ProfileController::class, 'update']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects/create', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::put('/projects/update/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/delete/{project}', [ProjectController::class, 'destroy']);
    Route::apiResource('skills', SkillController::class)->except(['create', 'edit', 'show']);
    Route::apiResource('experiences', ExperienceController::class)->except(['create', 'edit', 'show']);
});
