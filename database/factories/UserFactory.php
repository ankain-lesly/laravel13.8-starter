<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->unique()->regexify('[a-z]{6}[0-9]{4}'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::User,
            'phone' => null,
            'address' => null,
            'country' => null,
            'kyc_verified_at' => null,
            'blocked_at' => null,
            'blocked_reason' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function kycVerified(): static
    {
        return $this->state(fn () => [
            'kyc_verified_at' => now(),
        ]);
    }

    public function blocked(?string $reason = null): static
    {
        return $this->state(fn () => [
            'blocked_at' => now(),
            'blocked_reason' => $reason,
        ]);
    }
}
