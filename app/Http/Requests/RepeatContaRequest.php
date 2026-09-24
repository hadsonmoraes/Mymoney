<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepeatContaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'count' => 'required|integer|min:1|max:120',
            'frequency' => 'required|in:weekly,biweekly,monthly,yearly,custom',
            'interval' => 'nullable|integer|min:1|max:365',
            'start_date' => 'nullable|date',
            'custom_value' => 'nullable|string',
            'operation_token' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'count.required' => 'A quantidade de repetições é obrigatória.',
            'count.min' => 'A quantidade mínima de repetições é 1.',
            'count.max' => 'A quantidade máxima de repetições é 120.',
            'frequency.required' => 'Selecione uma frequência válida.',
            'frequency.in' => 'Frequência selecionada inválida.',
            'interval.min' => 'O intervalo mínimo deve ser 1.',
            'interval.max' => 'O intervalo máximo deve ser 365.',
            'start_date.date' => 'A data inicial deve ser uma data válida.',
        ];
    }
}
