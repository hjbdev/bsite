<?php

namespace App\Http\Requests\SeriesVetos;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeriesVetoRequest extends FormRequest
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
            'map_id' => ['required', 'exists:maps,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'type' => ['required', 'in:pick,ban,left-over'],
        ];
    }
}
