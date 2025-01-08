<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            'device_id' => 'nullable|string',
            'device_name' => 'nullable|string',
            'otp' => 'string|size:6|nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'Invalid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'otp.size' => 'OTP must be exactly 6 characters long',
        ];
    }
}