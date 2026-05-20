<?php

namespace Hopla\UploadManagement\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateMultipartUploadRequest extends FormRequest
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
            'filename' => 'required|string',
            'type' => 'nullable|string',
            'path' => 'nullable|string',
            'parent_id' => 'nullable|string',
            'size' => 'nullable|int',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'filename.required' => 'Le nom de fichier est obligatoire.',
            'filename.string' => 'Le nom du fichier doit être une chaine de caractere.',
            'size.int' => 'La taille doit être un entier.',
        ];
    }
}
