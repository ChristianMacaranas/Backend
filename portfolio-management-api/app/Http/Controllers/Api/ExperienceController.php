<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Admin;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    
    public function index(): JsonResponse
    {
        /** @var Admin|null $user */
        $user = Auth::user();
        $experiences = $user
            ->experiences()
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(ExperienceResource::collection($experiences));
    }

    public function store(ExperienceRequest $request): JsonResponse
    {
        /** @var Admin|null $user */
        $user = Auth::user();
        $experience = $user->experiences()->create($request->validated());
        return response()->json(new ExperienceResource($experience), 201);
    }

    public function update(ExperienceRequest $request, Experience $experience): JsonResponse
    {
        $this->authorize($experience);
        $experience->update($request->validated());
        return response()->json(new ExperienceResource($experience));
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $this->authorize($experience);
        $experience->delete();
        return response()->json(null, 204);
    }

    private function authorize(Experience $experience): void
    {
        /** @var Admin|null $user */
        $user = Auth::user();
        abort_unless(
            $experience->admin_id === $user->id,
            403,
            'You are not allowed to modify this experience.'
        );
    }
}
