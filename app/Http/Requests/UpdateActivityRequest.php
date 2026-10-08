<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'activity_date' => ['required', 'date'],
            'is_tentative' => ['boolean'],
            'start_time' => ['nullable', 'required_if:is_tentative,false', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'location_input' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'organizer_input' => ['nullable', 'string', 'max:255'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'organizer_text' => ['nullable', 'string', 'max:255'],
            'leader_id' => ['nullable', 'exists:leaders,id'],
            'companion_ids' => ['nullable', 'array'],
            'companion_ids.*' => ['exists:leaders,id'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:255'],
            'adc' => ['nullable', 'string', 'max:255'],
            'is_disposition' => ['boolean'],
            'disposition_to_id' => ['nullable', 'exists:leaders,id'],
            'protocol_officer_id' => ['nullable', 'exists:protocol_officers,id'],
            'dress_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
