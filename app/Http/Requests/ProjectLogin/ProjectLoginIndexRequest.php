<?php

namespace App\Http\Requests\ProjectLogin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectLoginIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:240'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }
}
