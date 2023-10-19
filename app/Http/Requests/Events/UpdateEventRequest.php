<?php

namespace App\Http\Requests\Events;

use App\Models\Organiser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->can('update:event')) {
            return true;
        }

        if ($user->can('update:(own)event')) {
            $organiser = Organiser::findOrFail($this->input('organiser_id'));

            return $organiser->users()->where('id', $user->id)->exists();
        }
    }

    public function rules(): array
    {
        return [
            'organiser_id' => ['required', 'exists:organisers,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'delay' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', File::types(['png'])->max(5192)],
            'prize_pool' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
        ];
    }
}
