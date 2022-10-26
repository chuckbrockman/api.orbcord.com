<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            // 'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => [
                'required',
                'min:8',
            ],
        ], [
            'code.required' => 'A valid beta user invite code is required',
            'code.exists' => 'A valid beta user invite code is required',
            'email.required' => 'A valid email address is required',
            'email.email' => 'A valid email address is required',
            'email.max' => 'The email address can only be 255 characters',
            'email.unique' => 'An email address already exists',
            'password.required' => 'A password is required and must be at least 8 characters',
            'password.min' => 'A password is required and must be at least 8 characters',
        ])->validate();

        return User::create([
            // 'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
