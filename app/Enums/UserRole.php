<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'tv_admin';
    case Master = 'tv_master';
}
