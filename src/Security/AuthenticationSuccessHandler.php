<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($token instanceof TwoFactorTokenInterface) {
            // Return the response to tell the client two-factor authentication is required.
            return new JsonResponse([
                'login' => 'success',
                'two_factor_required' => true,
                'two_factor_complete' => false,
            ]);
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        return new JsonResponse([
            'login' => 'success',
            'two_factor_required' => false,
        ]);
    }
}
