<?php

namespace App\Http\Requests\Setting;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class DatabaseSettingUpdateRequest extends FormRequest
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
        $databaseConnections = Setting::getDatabaseConnections();
        $databaseConnectionsKeys = array_keys($databaseConnections);

        // set default value for database_password
        $this->merge([
            'database_password' => $this->database_password ?? '',
        ]);

        return [
            'database_driver' => [
                'required',
                'string',
                'in:'.implode(',', $databaseConnectionsKeys),
            ],
            'database_host' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'database_port' => [
                'required',
                'numeric',
            ],
            'database_database' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'database_username' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'database_password' => [
                'nullable',
                'string',
                'max:255',
                'min:3',
            ],
        ];
    }
}
