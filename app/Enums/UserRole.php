<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case ProjectOfficer = 'project_officer';
    case ITAdmin = 'it_admin';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::ProjectOfficer => 'Project Officer',
            self::ITAdmin => 'IT Admin',
        };
    }
}
