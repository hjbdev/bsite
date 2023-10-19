<?php

namespace App\Http\Requests\Players;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdatePlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        return $user->can('update:player');
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
