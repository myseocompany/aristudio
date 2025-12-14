<?php

namespace App\Http\Requests\Timer;

use Illuminate\Foundation\Http\FormRequest;

class TimerStartRequest extends FormRequest
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
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'task_label' => ['required', 'string', 'max:240'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'project_name' => ['nullable', 'string', 'max:240'],
        ];
    }
}
