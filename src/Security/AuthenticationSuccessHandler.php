<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\TwoFactorStatusType;
use App\Entity\User;
use App\Exception\InvalidArgumentException;
use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    private function checkUserData(User $user): void
    {
        if (null !== $user->getGoogleAuthenticatorSecret() && $user->getTwoFactorStatus() !== TwoFactorStatusType::GOOGLE_AUTHENTICATOR) {
            $user->setGoogleAuthenticatorSecret(null);
            $this->userRepository->save($user, true);
        }

        if ($user->getTwoFactorStatus() === TwoFactorStatusType::PENDING) {
            $user->setTwoFactorStatus(TwoFactorStatusType::DISABLED);
            $this->userRepository->save($user, true);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($token instanceof TwoFactorTokenInterface) {
            $user = $token->getUser();
            if (!$user instanceof User) {
                throw new InvalidArgumentException('unexpected properties for authentication');
            }

            $this->checkUserData($user);

            // Return the response to tell the client two-factor authentication is required.
            return new JsonResponse([
                'login' => 'success',
                'two_factor_required' => true,
                'two_factor_complete' => false,
                'two_factor_method'   => $user->getTwoFactorStatus()->value,
            ]);
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        $this->checkUserData($user);

        return new JsonResponse([
            'login' => 'success',
            'two_factor_required' => false,
        ]);
    }
}
