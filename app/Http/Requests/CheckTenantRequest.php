<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckTenantRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tenant_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'tenant_name.required' => __('client.tenant_name_required'),
            'tenant_name.string' => __('client.tenant_name_invalid'),
        ];
    }
}
