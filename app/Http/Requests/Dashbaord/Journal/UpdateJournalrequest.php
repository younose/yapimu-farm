<?php

namespace App\Http\Requests\Dashbaord\Journal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalrequest extends FormRequest
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
            'period_id' => ['required', 'exists:periods,id'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'debit' => ['required', 'numeric'],
            'credit' => ['required', 'numeric'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }
}
