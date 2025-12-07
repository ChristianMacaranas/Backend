<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes|required';

        return [
            'title' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'tools' => ['nullable', 'string', 'max:255'],
            'status' => [$required, 'in:draft,published'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:2048'],
        ];
    }
}
