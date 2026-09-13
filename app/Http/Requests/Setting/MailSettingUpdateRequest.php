<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class MailSettingUpdateRequest extends FormRequest
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
            'mail_mailer' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_host' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_port' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_username' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_password' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_encryption' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
            'mail_from_address' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
        ];
    }
}
