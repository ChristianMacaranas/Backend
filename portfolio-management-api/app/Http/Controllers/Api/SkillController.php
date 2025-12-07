<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Skill\SkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = auth()->user()
            ->skills()
            ->orderByDesc('proficiency')
            ->orderBy('name')
            ->get();

        return response()->json(SkillResource::collection($skills));
    }

    public function store(SkillRequest $request): JsonResponse
    {
        $skill = $request->user()->skills()->create($request->validated());

        return response()->json(new SkillResource($skill), 201);
    }

    public function update(SkillRequest $request, Skill $skill): JsonResponse
    {
        $this->authorizeOwner($skill);
        $skill->update($request->validated());

        return response()->json(new SkillResource($skill));
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $this->authorizeOwner($skill);
        $skill->delete();

        return response()->json(['message' => 'Skill deleted successfully.']);
    }

    private function authorizeOwner(Skill $skill): void
    {
        abort_unless(
            $skill->admin_id === auth()->id(),
            403,
            'You are not allowed to modify this skill.'
        );
    }
}
