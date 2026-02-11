<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    protected function prepareForValidation()
    {
        if ($this->has('password') && trim($this->password) === '') {
            $this->merge(['password' => null]);
        }
    }

    public function rules()
    {
        $user = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'role' => ['required', 'string', 'in:admin,technicien,employe'],
        ];

        if ($this->filled('password')) {
            $rules['password'] = ['string', 'min:8'];
        }

        return $rules;
    }
}
