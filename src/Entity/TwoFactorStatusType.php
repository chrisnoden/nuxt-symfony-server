<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Behaviours\EnumValues;

enum TwoFactorStatusType: string
{
    use EnumValues;

    case DISABLED = 'disabled';
    case PENDING = 'pending';
    case GOOGLE_AUTHENTICATOR = 'google-authenticator';
}
