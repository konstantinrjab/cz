<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) Auth::user()->getAuthIdentifier();
    }

    public function rules(): array
    {
        return [
            'name' => 'string|min:3|required|unique:games',
            'password' => 'string|min:3'
        ];
    }
}
