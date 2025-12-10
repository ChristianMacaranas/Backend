<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SkillResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;

class PublicPortfolioController extends Controller
{
    public function index(): JsonResponse
    {
        $admin = Admin::first();

        if (! $admin) {
            return response()->json([
                'message' => 'Portfolio has not been configured yet.',
            ], 404);
        }

        return response()->json([
            'admin' => new AdminResource($admin),
            'projects' => ProjectResource::collection(
                $admin->projects()->published()->latest()->get()
            ),
            'skills' => SkillResource::collection(
                $admin->skills()->orderByDesc('proficiency')->orderBy('name')->get()
            ),
            'experiences' => ExperienceResource::collection(
                $admin->experiences()->orderByDesc('start_date')->get()
            ),
        ]);
    }
}
