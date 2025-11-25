<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ManageBooking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'status' => 'sometimes|required|in:pending,confirmed,cancelled,rescheduled',
            'attendee_email' => 'nullable|email|max:255',
            'attendee_name' => 'nullable|string|max:255',
            'attendee_phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Booking title is required',
            'title.max' => 'Booking title cannot exceed 255 characters',
            'start_time.required' => 'Start time is required',
            'start_time.date' => 'Start time must be a valid date',
            'end_time.required' => 'End time is required',
            'end_time.date' => 'End time must be a valid date',
            'end_time.after' => 'End time must be after start time',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: pending, confirmed, cancelled, rescheduled',
            'attendee_email.email' => 'Attendee email must be a valid email address',
            'attendee_email.max' => 'Attendee email cannot exceed 255 characters',
            'attendee_name.max' => 'Attendee name cannot exceed 255 characters',
            'attendee_phone.max' => 'Attendee phone cannot exceed 50 characters',
            'location.max' => 'Location cannot exceed 255 characters',
        ];
    }
}
