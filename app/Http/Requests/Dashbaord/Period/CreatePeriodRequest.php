<?php

namespace App\Http\Requests\Dashbaord\Period;

use Illuminate\Foundation\Http\FormRequest;

class CreatePeriodRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'date',
                'after:start_date',
            ],
            'management_fee' => [
                'required',
                'numeric',
                'min:0',
            ],
            'cooperative_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'zakat_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }
}
