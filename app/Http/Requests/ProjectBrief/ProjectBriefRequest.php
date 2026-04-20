<?php

namespace App\Http\Requests\ProjectBrief;

use Illuminate\Foundation\Http\FormRequest;

class ProjectBriefRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:10000'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:20480'],
            'selected_options' => ['nullable', 'array'],
            'selected_options.*' => ['nullable', 'array'],
            'selected_options.*.*' => ['integer', 'exists:project_meta_datas,id'],
            'access_logins' => ['nullable', 'array'],
            'access_logins.*.name' => ['nullable', 'string', 'max:255'],
            'access_logins.*.user' => ['nullable', 'string', 'max:255'],
            'access_logins.*.password' => ['nullable', 'string', 'max:255'],
            'access_logins.*.url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
