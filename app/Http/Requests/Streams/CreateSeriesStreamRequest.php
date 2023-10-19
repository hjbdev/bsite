<?php

namespace App\Http\Requests\Streams;

use Illuminate\Foundation\Http\FormRequest;

class CreateSeriesStreamRequest extends FormRequest
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

        return $user->can('store:stream');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'url' => ['required', 'url'],
            'platform' => ['required', 'string', 'in:youtube,twitch,kick'],
        ];
    }
}
