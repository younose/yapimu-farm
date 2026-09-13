<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountUpdateRequest extends FormRequest
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
            'account_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'bank_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'account_number' => [
                'required',
                'numeric',
                'min:3',
            ],
        ];
    }
}
