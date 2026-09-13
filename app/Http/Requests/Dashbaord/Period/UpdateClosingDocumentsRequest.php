<?php

namespace App\Http\Requests\Dashbaord\Period;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClosingDocumentsRequest extends FormRequest
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
            'rhpp_document' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'rhpp_nominal' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'rhpp_transfer_proof' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'rhpp_transfer_nominal' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }
}
