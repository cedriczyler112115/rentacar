<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hasProofUploads = $this->hasFile('legitimacy_proofs');
        $isLegitimacyForm = (string) $this->input('legitimacy_form', '0') === '1';

        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'legitimacy_proofs' => ['nullable', 'array', 'max:15'],
            'legitimacy_proofs.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'legitimacy_form' => ['nullable', 'in:0,1'],
            'legitimacy_terms' => ($hasProofUploads || $isLegitimacyForm) ? ['required', 'accepted'] : ['nullable'],
            'about_owner' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
