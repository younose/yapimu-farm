<?php

namespace App\Http\Requests\Dashbaord\Period;

use Illuminate\Foundation\Http\FormRequest;

class CloseClosingPeriodRequest extends FormRequest
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
            'revenue' => [
                'required',
                'numeric',
                'min:0',
            ],
            'rhpp_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'rhpp_nominal' => [
                'required',
                'numeric',
                'min:0',
            ],
            'rhpp_transfer_proof' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'rhpp_transfer_nominal' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rhpp_document.required' => 'Dokumen RHPP wajib diunggah saat menutup periode.',
            'rhpp_nominal.required' => 'Nominal RHPP wajib diisi sesuai dokumen RHPP.',
            'rhpp_transfer_proof.required' => 'Bukti transfer masuk RHPP wajib diunggah saat menutup periode.',
            'rhpp_transfer_nominal.required' => 'Nominal transfer aktual wajib diisi.',
        ];
    }
}
