<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'string'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'item_type' => ['required', 'in:clothes,comforter,shoes'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_date.required' => 'Booking date is required',
            'booking_date.after_or_equal' => 'Booking date must be today or later',
            'booking_time.required' => 'Booking time is required',
            'pickup_address.required' => 'Pickup address is required',
            'item_type.required' => 'Item type is required',
            'item_type.in' => 'Invalid item type selected',
            'services.*.exists' => 'Invalid service selected',
            'products.*.exists' => 'Invalid product selected',
        ];
    }
}
