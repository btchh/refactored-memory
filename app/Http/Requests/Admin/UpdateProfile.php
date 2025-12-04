<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfile extends FormRequest
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
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return [];
        }

        return [
            'username' => 'required|string|unique:admins,username,' . $admin->id,
            'branch_name' => 'required|string',
            'fname' => 'required|string',
            'lname' => 'required|string',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/', 'unique:admins,phone,' . $admin->id],
            'address' => 'required|string',
            'branch_address' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.unique' => 'Username is already taken',
            'branch_name.required' => 'Branch name is required',
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already registered',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be a valid Philippine mobile number',
            'phone.unique' => 'Phone number is already registered',
            'address.required' => 'Address is required',
        ];
    }
}
