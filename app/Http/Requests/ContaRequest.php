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
        ];
    }
}
