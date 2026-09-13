<?php

namespace App\Http\Requests\Dashbaord\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateRequest extends FormRequest
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
        $userId = $this->route('investor');

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
                'unique:users,username,'.$userId,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,'.$userId,
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
            ],
            'role' => [
                'required',
                'string',
                'in:admin,teller',
            ],
            'photo' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ];
    }
}
