<?php

namespace Hopla\UploadManagement\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AbortMultipartUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uploadId' => 'required|string',
            'key' => 'required|string',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'uploadId.required' => 'L’identifiant de l’upload est obligatoire.',
            'uploadId.string' => 'L’identifiant de l’upload doit être une chaîne de caractères.',
            'key.required' => 'La clé est obligatoire.',
            'key.string' => 'La clé doit être une chaîne de caractères.',
        ];
    }
}
