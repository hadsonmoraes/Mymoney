<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
           'name' => ['required',  Rule::unique('category')->where('user_id', Auth::id())]
        ];
    }

        public function messages(): array
    {
        return [
            'name.required' => 'Campo nome é obrigatório!',
            'name.unique' => 'O nome fornecido já está em uso.',
        ];
    }
}
