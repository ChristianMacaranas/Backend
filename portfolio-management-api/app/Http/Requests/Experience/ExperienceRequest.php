<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes|required';

        return [
            'role' => [$required, 'string', 'max:255'],
            'company' => [$required, 'string', 'max:255'],
            'start_date' => [$required, 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
