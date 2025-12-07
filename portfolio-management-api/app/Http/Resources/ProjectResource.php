<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'tools' => $this->tools,
            'status' => $this->status,
            'images' => $this->formatImages(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }

    private function formatImages(): ?array
    {
        if (! $this->images) {
            return null;
        }

        return collect($this->images)
            ->filter()
            ->map(fn ($path) => Storage::disk('public')->url($path))
            ->values()
            ->all();
    }
}
