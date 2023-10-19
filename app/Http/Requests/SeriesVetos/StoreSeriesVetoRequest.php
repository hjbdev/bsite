<?php

namespace App\Http\Requests\SeriesVetos;

use App\Models\Series;
use Illuminate\Foundation\Http\FormRequest;

class StoreSeriesVetoRequest extends FormRequest
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

        if ($user->can('store:veto')) {
            return true;
        }

        if ($user->can('store:(own)veto')) {
            $series = Series::findOrFail($this->route('series'))->with('event.organiser');

            return $series->event->organiser->users()->where('id', $user->id)->exists();
        }

        return false;
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
