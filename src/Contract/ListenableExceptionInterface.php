<?php

declare(strict_types=1);

namespace App\Contract;

interface ListenableExceptionInterface
{
    public function getStatusCode(): int;

    public function toArray(): array;
}
