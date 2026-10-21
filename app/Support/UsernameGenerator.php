<?php

namespace App\Support;

use App\Models\User;

class UsernameGenerator
{
    public static function fromEmail(string $email, ?string $ignoreUserId = null): string
    {
        $local = strtolower(strstr($email, '@', true) ?: '');
        $slug = preg_replace('/[^a-z0-9_]/', '', $local) ?? '';
        $slug = trim($slug, '_');

        if ($slug === '') {
            $slug = 'user';
        }

        if (strlen($slug) < 3) {
            $slug = str_pad($slug, 3, '0');
        }

        $base = substr($slug, 0, 20);
        $candidate = $base;
        $n = 0;

        while (self::taken($candidate, $ignoreUserId)) {
            $n++;
            $suffix = (string) $n;
            $candidate = substr($base, 0, 20 - strlen($suffix)).$suffix;
        }

        return $candidate;
    }

    private static function taken(string $username, ?string $ignoreUserId): bool
    {
        return User::query()
            ->where('username', $username)
            ->when($ignoreUserId !== null, fn ($q) => $q->where('id', '!=', $ignoreUserId))
            ->exists();
    }
}
