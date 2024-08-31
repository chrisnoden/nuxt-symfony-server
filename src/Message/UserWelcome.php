<?php

declare(strict_types=1);

namespace App\Message;

use App\Message\Behaviours\AsyncMessageInterface;
use Ramsey\Uuid\UuidInterface;

readonly class UserWelcome implements AsyncMessageInterface
{
    public function __construct(
        private UuidInterface $userId,
    ) {
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}
