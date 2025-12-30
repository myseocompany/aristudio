<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BillingReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'month' => ['required', 'date_format:Y-m'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'month' => $this->input('month', now()->format('Y-m')),
            'user_id' => $this->input('user_id', $this->user()?->id),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'month.required' => 'Seleccione un mes para generar la cuenta de cobro.',
            'month.date_format' => 'El mes debe tener el formato AAAA-MM.',
            'user_id.required' => 'Seleccione un usuario.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
        ];
    }
}
