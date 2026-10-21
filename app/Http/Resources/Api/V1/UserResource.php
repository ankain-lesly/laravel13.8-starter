<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\KycStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing('wallet');

        $avatarUrl = $this->avatarUrl();

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'role' => $this->role->value,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country,
            'avatar' => $avatarUrl,
            'kyc_status' => $this->kyc_status?->value ?? KycStatus::NotStarted->value,
            'kyc_verified_at' => $this->kyc_verified_at?->toIso8601String(),
            'is_activated' => $this->isActivated(),
            'wallet' => $this->wallet ? new WalletResource($this->wallet) : null,
        ];
    }

    private function avatarUrl(): ?string
    {
        $path = (string) $this->avatar;

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'data:')) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
