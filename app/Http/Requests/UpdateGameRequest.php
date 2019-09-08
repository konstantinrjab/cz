<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool)Auth::user()->getAuthIdentifier();
    }

    public function rules(): array
    {
        return [
            'row'    => 'integer|required|min:1|max:3',
            'column' => 'integer|required|min:1|max:3'
        ];
    }
}
