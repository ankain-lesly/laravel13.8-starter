<?php

namespace App\Models;

use App\Enums\KycStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property UserRole $role
 * @property KycStatus $kyc_status
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUlids, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'country',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'kyc_status' => 'not_started',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'blocked_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'kyc_status' => KycStatus::class,
        ];
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::Master], true);
    }

    public function kycSubmission(): HasOne
    {
        return $this->hasOne(KycSubmission::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function accountActivation(): HasOne
    {
        return $this->hasOne(AccountActivation::class);
    }

    public function isActivated(): bool
    {
        return $this->accountActivation?->isCompleted() ?? false;
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_status === KycStatus::Verified;
    }

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }
}
