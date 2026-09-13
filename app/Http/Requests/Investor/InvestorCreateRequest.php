<?php

namespace App\Http\Requests\Investor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class InvestorCreateRequest extends FormRequest
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
        $totalShares = getSetting('default_share_count');
        $aqumulatedShares = User::investors()->sum('shares');
        $availableShares = $totalShares - $aqumulatedShares;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'unique:users',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
            'shares' => [
                'required',
                'numeric',
                'min:0',
                'max:'.$availableShares,
            ],
            'photo' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ];
    }
}
