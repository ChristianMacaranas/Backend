<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Admin;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
{
    /** @var Admin $user */
    $user = auth()->user();
    $query = $user
        ->projects()
        ->latest();

    
    if (request()->filled('search')) {
        $search = request()->search;

        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if (request()->filled('category')) {
        $query->where('category', request()->category);
    }

    if (request()->filled('status')) {
        $query->where('status', request()->status);
    }

    $projects = $query->get();

    return response()->json(ProjectResource::collection($projects));
}


    public function store(ProjectRequest $request): JsonResponse
    {
        $admin = $request->user();
        $project = $admin->projects()->create($this->buildPayloadFromRequest($request));

        return response()->json(new ProjectResource($project), 201);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorizeOwner($project);

        return response()->json(new ProjectResource($project));
    }

    public function update(ProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorizeOwner($project);
        $project->update($this->buildPayloadFromRequest($request));

        return response()->json(new ProjectResource($project));
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorizeOwner($project);
        $project->delete();

        return response()->json(null, 204);
    }

    private function buildPayloadFromRequest(ProjectRequest $request): array
    {
        $payload = $request->safe()->except(['images']);

        if ($request->hasFile('images')) {
            $payload['images'] = collect($request->file('images'))
                ->map(fn ($file) => $file->store('uploads/projects', 'public'))
                ->values()
                ->all();
        }

        return $payload;
    }

    private function authorizeOwner(Project $project): void
    {
        /** @var Admin $user */
        $user = auth()->user();
        abort_unless(
            $project->admin_id === $user->id,
            403,
            'You are not allowed to modify this project.'
        );
    }
}
