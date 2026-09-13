<?php

namespace App\Http\Requests\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawCreateRequest extends FormRequest
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
        $balance = auth()->user()->balance;

        return [
            'transfer_type' => [
                'required',
                'in:cash,bank',
            ],
            'bank_account_id' => [
                'numeric',
                'required_if:transfer_type,bank',
            ],
            'amount' => [
                'required',
                'min:10000',
                'max:'.$balance,
                'numeric',
            ],
        ];
    }
}
