<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $deviceId = request()->cookie('device_id');

        $banned = \App\Models\BannedIdentifier::where(function ($query) use ($input, $deviceId) {
            $query->where('type', 'email')->where('value', $input['email']);
            if ($deviceId) {
                $query->orWhere(function ($q) use ($deviceId) {
                    $q->where('type', 'device_cookie')->where('value', $deviceId);
                });
            }
            if (!empty($input['phone_number'])) {
                $query->orWhere(function ($q) use ($input) {
                    $q->where('type', 'phone_number')->where('value', $input['phone_number']);
                });
            }
        })->exists();

        if ($banned) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => __('This account cannot be created due to a policy violation.'),
            ]);
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'device_id' => $deviceId,
        ]);
    }
}
