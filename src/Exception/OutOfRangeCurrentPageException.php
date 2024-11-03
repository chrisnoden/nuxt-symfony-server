<?php

declare(strict_types=1);

namespace App\Exception;

use App\Contract\ListenableExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class OutOfRangeCurrentPageException extends InvalidArgumentException implements ListenableExceptionInterface
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function toArray(): array
    {
        return [
            'message' => 'No results, bad pagination request',
        ];
    }
}
