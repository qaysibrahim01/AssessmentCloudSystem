<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'company_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($input) {
                    if (($input['role'] ?? null) === 'committee' && empty($value)) {
                        $fail('The company name field is required for committee users.');
                    }
                },
            ],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            // Admin sign-ups are blocked; admins are created by the master admin only
            'role' => in_array($input['role'] ?? null, ['assessor', 'committee'])
                ? $input['role']
                : 'assessor',
            'company_name' => $input['company_name'] ?? null,
            'approval_status' => 'pending',
            'approved_at' => null,
            'is_approved' => false,
            'approval_email_sent_at' => null,
        ]);
    }
}
