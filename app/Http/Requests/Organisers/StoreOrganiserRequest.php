<?php

namespace App\Http\Requests\Organisers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreOrganiserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:organisers,name'],
            'logo' => ['nullable', File::types(['png'])->max(5192)],
        ];
    }
}
