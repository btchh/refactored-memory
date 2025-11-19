<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'otp' => 'required|string|size:6',
            'username' => 'required|string|unique:users,username',
            'fname' => 'required|string',
            'lname' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
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
            'otp.required' => 'OTP code is required',
            'otp.size' => 'OTP code must be 6 digits',
            'username.required' => 'Username is required',
            'username.unique' => 'Username is already taken',
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'address.required' => 'Address is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
