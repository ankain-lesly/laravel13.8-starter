<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict admin API routes to admin and master roles only.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        $allowedRoles = [UserRole::Admin, UserRole::Master];

        if (! in_array($user->role, $allowedRoles, true)) {
            return ApiResponse::error('Forbidden.', 403);
        }

        return $next($request);
    }
}
