<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\AdminResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $admin = $request->user();
        $payload = $request->safe()->except(['profile_image']);

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image_path) {
                Storage::disk('public')->delete($admin->profile_image_path);
            }

            $payload['profile_image_path'] = $request->file('profile_image')->store('uploads/profile', 'public');
        }

        $admin->update($payload);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => new AdminResource($admin->fresh()),
        ]);
    }
}
