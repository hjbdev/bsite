<?php

namespace App\Http\Requests\Players;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePlayerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string_or_array'],
            'steam_id3' => ['required', 'string', 'max:255'],
            'steam_id64' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'picture' => ['nullable', File::types(['png', 'jpg'])->max(5192)],
        ];
    }
}
