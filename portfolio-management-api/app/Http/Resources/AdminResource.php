<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'bio' => $this->bio,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_image_url' => $this->profile_image_path
                ? Storage::disk('public')->url($this->profile_image_path)
                : null,
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
