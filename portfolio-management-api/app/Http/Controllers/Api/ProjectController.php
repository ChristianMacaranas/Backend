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
        $projects = auth()->user()
            ->projects()
            ->latest()
            ->get();

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

        return response()->json(['message' => 'Project deleted successfully.']);
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
        abort_unless(
            $project->admin_id === auth()->id(),
            403,
            'You are not allowed to modify this project.'
        );
    }
}
