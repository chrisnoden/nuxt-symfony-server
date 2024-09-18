<?php

declare(strict_types=1);

namespace App\EventListeners;

use App\Contract\ListenableExceptionInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
final class CustomExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ListenableExceptionInterface) {
            return;
        }

        $response = new JsonResponse($exception->toArray(), $exception->getStatusCode());
        $event->setResponse($response);
    }
}
