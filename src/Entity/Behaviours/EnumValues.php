<?php

declare(strict_types=1);

namespace App\Entity\Behaviours;

trait EnumValues
{
    /**
     * @return array all the possible enum values as an array
     */
    public static function values(): array
    {
        return array_map(fn($c) => $c->value, static::cases());
    }
}
