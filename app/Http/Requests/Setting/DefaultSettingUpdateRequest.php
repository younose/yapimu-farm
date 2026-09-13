<?php

namespace App\Http\Requests\Setting;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DefaultSettingUpdateRequest extends FormRequest
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
        $shares = User::investors()->sum('shares');

        return [
            'default_zakat_percentage' => [
                'required',
                'numeric',
                'min:0',
            ],
            'default_cooperative_percentage' => [
                'required',
                'numeric',
                'min:0',
            ],
            'default_management_fee' => [
                'required',
                'numeric',
                'min:0',
            ],
            'default_share_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'default_share_count' => [
                'required',
                'numeric',
                'min:'.$shares ?? 0,
            ],
        ];
    }
}
