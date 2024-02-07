<?php

namespace App\Http\Requests\SeriesMaps;

use App\Models\Series;
use Illuminate\Foundation\Http\FormRequest;

class DestroySeriesMapRequest extends FormRequest
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

        if ($user->can('destroy:series-map')) {
            return true;
        }

        if ($user->can('destroy:(own)series-map')) {
            $series = Series::with('event.organiser')->findOrFail($this->route('series'));

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
            //
        ];
    }
}
