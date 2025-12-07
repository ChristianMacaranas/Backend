<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('admins', 'username')->ignore($this->user()?->id),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
