<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}