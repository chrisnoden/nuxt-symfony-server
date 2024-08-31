<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class TwoFactorAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        // Return the response to tell the client that 2fa failed. You may want to add more details
        // from the $exception.
        return new JsonResponse([
            'error'               => '2fa_failed',
            'two_factor_complete' => false,
        ]);
    }
}
