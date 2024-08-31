<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Behaviours\StandardResponses;
use App\Entity\TwoFactorStatusType;
use App\Entity\User;
use App\FormRequest\DisableTwoFactorFormRequest;
use App\FormRequest\EnableTwoFactorFormRequest;
use App\Repository\UserRepository;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/security/2fa',
    name: 'security_2fa_',
)]
#[IsGranted('IS_AUTHENTICATED')]
class SecurityTwoFactorController extends AbstractController
{
    use StandardResponses;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(
        path: '/disable',
        name: 'disable_two_factor',
    )]
    public function disableTwoFactor(
        #[CurrentUser] User $user,
        DisableTwoFactorFormRequest $request,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        if (!$passwordHasher->isPasswordValid($user, $request->get('password'))) {
            return $this->badRequest('invalid password');
        }

        if (!$user->isGoogleAuthenticatorEnabled()) {
            return $this->badRequest('two factor not enabled');
        }

        $user
            ->setTwoFactorStatus(TwoFactorStatusType::DISABLED)
            ->setGoogleAuthenticatorSecret(null)
        ;
        $this->userRepository->save($user, true);

        return $this->okResponse();
    }

    #[Route(
        path: '/enable',
        name: 'enable_two_factor',
    )]
    public function enableTwoFactor(
        #[CurrentUser] User $user,
        EnableTwoFactorFormRequest $request,
        GoogleAuthenticatorInterface $authenticator,
    ): JsonResponse {
        if ($user->isGoogleAuthenticatorEnabled()) {
            return $this->badRequest('two factor already enabled');
        }

        if ($authenticator->checkCode($user, $request->get('authCode'))) {
            $user
                ->setTwoFactorStatus(TwoFactorStatusType::GOOGLE_AUTHENTICATOR)
            ;
            $this->userRepository->save($user, true);

            return $this->okResponse();
        }

        return $this->badRequest('invalid code');
    }

    #[Route(
        path: '/qr_code',
        name: 'qr_code',
    )]
    public function qrCode(
        #[CurrentUser] User $user,
        GoogleAuthenticatorInterface $authenticator,
    ): Response {
        if ($user->isGoogleAuthenticatorEnabled()) {
            return $this->badRequest('2fa already enabled');
        }

        if (null === $user->getGoogleAuthenticatorSecret()) {
            $user
                ->setTwoFactorStatus(TwoFactorStatusType::PENDING)
                ->setGoogleAuthenticatorSecret($authenticator->generateSecret())
            ;
            $this->userRepository->save($user, true);
        }

        $qrCodeContent = $authenticator->getQRContent($user);
        $result        = Builder::create()
            ->data($qrCodeContent)
            ->build()
        ;
        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
