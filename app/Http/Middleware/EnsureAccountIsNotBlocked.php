<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsNotBlocked
{
    /**
     * Stop blocked customer accounts from using authenticated routes.
     * Admin and master roles are not gated.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        if ($user->hasAdminAccess()) {
            return $next($request);
        }

        if ($user->blocked_at) {
            return ApiResponse::error('This account is blocked.', 403);
        }

        return $next($request);
    }
}
