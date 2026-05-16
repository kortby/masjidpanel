<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'provider', 'provider_id', 'avatar', 'city', 'zip_code', 'phone_number', 'show_phone', 'show_email', 'is_verified', 'age', 'address', 'show_age', 'show_location', 'show_address', 'language', 'device_id', 'banned_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'is_verified' => 'boolean',
            'age' => 'integer',
            'show_age' => 'boolean',
            'show_location' => 'boolean',
            'show_address' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
