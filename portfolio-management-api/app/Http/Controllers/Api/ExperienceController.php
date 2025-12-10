<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use Illuminate\Http\JsonResponse;

class ExperienceController extends Controller
{
    
    public function index(): JsonResponse
    {
        $experiences = auth()->user()
            ->experiences()
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(ExperienceResource::collection($experiences));
    }
}
