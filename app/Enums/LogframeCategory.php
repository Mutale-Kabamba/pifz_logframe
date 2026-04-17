<?php

declare(strict_types=1);

namespace App\Enums;

enum LogframeCategory: string
{
    case Impact = 'impact';
    case Outcome = 'outcome';
    case Output = 'output';
    case Activity = 'activity';

    public function label(): string
    {
        return match ($this) {
            self::Impact => 'Impact',
            self::Outcome => 'Outcome',
            self::Output => 'Output',
            self::Activity => 'Activity',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Impact => 'danger',
            self::Outcome => 'warning',
            self::Output => 'success',
            self::Activity => 'info',
        };
    }
}
