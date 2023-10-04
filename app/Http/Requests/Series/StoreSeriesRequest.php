<?php

namespace App\Http\Requests\Series;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeriesRequest extends FormRequest
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
            'event.id' => ['nullable', 'exists:events,id'], #
            'team_a.id' => ['required', 'exists:teams,id'],
            'team_b.id' => ['required', 'exists:teams,id'],
            'team_a_score' => ['required', 'integer', 'min:0'],
            'team_b_score' => ['required', 'integer', 'min:0'],
            'type.id' => ['required', 'in:bo1,bo3,bo5,bo7,bo9'],
            'status' => ['required', 'in:upcoming,ongoing,completed'],
        ];
    }
}
