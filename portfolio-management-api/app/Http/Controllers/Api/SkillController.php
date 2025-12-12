<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Skill\SkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Admin;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    
    public function index(): JsonResponse
    {
        /** @var Admin $user */
        $user = auth()->user();
        $skills = $user
            ->skills()
            ->orderByDesc('proficiency')->orderBy('name')
            ->get();

        return response()->json(SkillResource::collection($skills));
    }

    public function store(SkillRequest $request): JsonResponse
    {
        /** @var Admin $user */
        $user = auth()->user();
        $skill = $user->skills()->create($request->validated());
        return response()->json(new SkillResource($skill), 201);
    }

    public function update(SkillRequest $request, Skill $skill): JsonResponse
    {
        $this->authorize($skill);
        $skill->update($request->validated());
        return response()->json(new SkillResource($skill));
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $this->authorize($skill);
        $skill->delete();
        return response()->json(null, 204);
    }

    private function authorize(Skill $skill): void
    {
        /** @var Admin $user */
        $user = auth()->user();
        abort_unless(
            $skill->admin_id === $user->id,
            403,
            'You are not allowed to modify this skill.'
        );
    }
}
