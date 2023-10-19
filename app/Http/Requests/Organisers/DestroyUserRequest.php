<?php

namespace App\Http\Requests\Organisers;

use App\Models\Organiser;
use Illuminate\Foundation\Http\FormRequest;

class DestroyUserRequest extends FormRequest
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

            if ($organiser && $organiser->users()->where('id', $user->id)->exists()) {
                return true;
            }
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
