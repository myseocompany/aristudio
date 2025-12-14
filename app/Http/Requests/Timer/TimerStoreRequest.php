<?php

namespace App\Http\Requests\Timer;

use App\Http\Controllers\TimerController;
use Illuminate\Foundation\Http\FormRequest;

class TimerStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:240'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'seconds' => ['required', 'integer', 'min:1', 'max:'.TimerController::MAX_SECONDS],
        ];
    }
}
