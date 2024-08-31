<?php

declare(strict_types=1);

namespace App\Security\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

readonly class SecurityRateLimiter implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $securityLimiter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Retrieve the request from the request event
        $request = $event->getRequest();

        // Check if the requested route name starts with security_
        if (str_starts_with($request->get("_route", ''), 'security_')) {
            // Retrieve the limiter based on the request client IP
            $limiter = $this->securityLimiter->create($request->getClientIp());

            // Consume one request and check if it's still accepted
            if (false === $limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException();
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // Only action route names that start with security_
        if (str_starts_with($request->get("_route", ''), 'security_')) {
            // Retrieve the limiter based on the request client IP
            $limiter = $this->securityLimiter->create($request->getClientIp());
            $limit   = $limiter->consume(0);

            // Set multiple headers simultaneously
            $response->headers->add([
                'X-RateLimit-Remaining'   => $limit->getRemainingTokens(),
                'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp() - time(),
                'X-RateLimit-Limit'       => $limit->getLimit(),
            ]);
        }
    }
}
