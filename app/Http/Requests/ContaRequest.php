<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaRequest extends FormRequest
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
            'name' => 'required',
            'value' => 'required',
            'maturity' => 'required',
            'type' => 'required',
            'situation' => 'required',
            'category_id' => 'required',
            'recurrence_type' => 'nullable|in:none,weekly,biweekly,monthly,yearly,custom',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_end_date' => 'nullable|date',
            'recurrence_max_occurrences' => 'nullable|integer|min:1|max:365',
            'update_scope' => 'nullable|in:only_this,this_and_next,all_sequence',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo nome é obrigatório!',
            'value.required' => 'Campo valor é obrigatório!',
            'type.required' => 'Campo tipo é obrigatório!',
            'maturity.required' => 'Campo vencimento é obrigatório!',
            'situation.required' => 'Campo situação é obrigatório!',
            'category_id.required' => 'Campo categoria é obrigatório!',
            'recurrence_type.in' => 'Tipo de recorrência selecionado inválido!',
            'recurrence_interval.min' => 'O intervalo mínimo de recorrência é 1 dia!',
            'update_scope.in' => 'Opção de atualização em lote inválida!',
        ];
    }
}
