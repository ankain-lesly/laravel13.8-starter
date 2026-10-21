<?php

namespace App\Support;

class TokenGenerator
{
    public static function generate(int $length = 16): string
    {
        $bytes = max(1, (int) ceil($length / 2));

        return substr(bin2hex(random_bytes($bytes)), 0, $length);
    }
}
