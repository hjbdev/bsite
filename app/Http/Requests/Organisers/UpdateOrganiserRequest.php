<?php

namespace App\Http\Requests\Organisers;

use App\Models\Organiser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateOrganiserRequest extends FormRequest
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

        if ($user->can('update:organiser')) {
            return true;
        }

        if ($user->can('update:(own)organiser')) {
            $organiser = Organiser::findOrFail($this->route('organiser'));

            return $organiser->users()->where('id', $user->id)->exists();
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
            'name' => ['required', 'string', Rule::unique('organisers')->ignore($this->route('organiser'))],
            'logo' => ['nullable', File::types(['png'])->max(5192)],
        ];
    }
}
