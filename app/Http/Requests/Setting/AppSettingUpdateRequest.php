<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class AppSettingUpdateRequest extends FormRequest
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
            'app_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'app_url' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'app_description' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'app_favicon' => [
                'max:512',
                'mimes:jpeg,jpg,png,gif,svg,ico,wepb',
            ],
            'app_logo_full' => [
                'max:512',
                'mimes:jpeg,jpg,png,gif,svg,ico,wepb',
            ],
            'app_logo' => [
                'max:512',
                'mimes:jpeg,jpg,png,gif,svg,ico,wepb',
            ],
        ];
    }
}
