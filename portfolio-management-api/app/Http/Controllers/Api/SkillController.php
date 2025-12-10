<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    
    public function index(): JsonResponse
    {
        $skills = auth()->user()
            ->skills()
            ->orderBy('name')
            ->get();

        return response()->json(SkillResource::collection($skills));
    }
}
