<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:3|confirmed',
        ];
    }
}
