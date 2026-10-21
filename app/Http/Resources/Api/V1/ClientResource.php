<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin array<string, mixed>
 */
class ClientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $row = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'id' => $row['id'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'country' => $row['country'],
            'kyc_status' => $row['kyc_status'],
            'blocked_at' => $row['blocked_at'],
            'blocked_reason' => $row['blocked_reason'] ?? null,
            'account_status' => $row['account_status'],
            'wallet' => $row['wallet'],
            'cards_count' => $row['cards_count'],
        ];
    }
}
