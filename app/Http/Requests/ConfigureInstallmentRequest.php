<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigureInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_installment' => 'required|boolean',
            'installment_number' => 'required_if:is_installment,1,true|nullable|integer|min:1|max:999',
            'installments_total' => 'required_if:is_installment,1,true|nullable|integer|min:1|max:999',
            'link_related' => 'nullable|boolean',
            'related_ids' => 'nullable|array',
            'related_ids.*' => 'integer|exists:contas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'installment_number.required_if' => 'Informe o número da parcela atual.',
            'installment_number.min' => 'A parcela atual deve ser no mínimo 1.',
            'installment_number.max' => 'A parcela atual não pode ultrapassar 999.',
            'installments_total.required_if' => 'Informe o total de parcelas.',
            'installments_total.min' => 'O total de parcelas deve ser no mínimo 1.',
            'installments_total.max' => 'O total de parcelas não pode ultrapassar 999.',
        ];
    }
}
