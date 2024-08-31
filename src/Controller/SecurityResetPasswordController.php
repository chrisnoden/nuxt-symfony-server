<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\FormRequest\UserResetPasswordBeginFormRequest;
use App\FormRequest\UserResetPasswordResetFormRequest;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route(
    path: '/security/reset-password',
    name: 'security_reset-password_',
)]
class SecurityResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerService $mailer,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
    ) {
    }

    #[Route(
        path: '/begin',
        name: 'begin',
        methods: ['POST'],
    )]
    public function begin(
        UserResetPasswordBeginFormRequest $request,
        UserRepository $userRepository,
    ): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $request->get('email')]);
        if ($user instanceof User) {
            try {
                $token = $this->resetPasswordHelper->generateResetToken($user);
                $this->mailer->sendPasswordResetEmail($user, $token->getToken());
            } catch (TooManyPasswordRequestsException) {
                return $this->badRequest('too many reset password requests');
            } catch (ResetPasswordExceptionInterface $e) {
                // unable to generate a token
                return $this->unexpectedErrorResponse($e->getReason());
            }
        }

        return $this->okResponse();
    }

    #[Route(
        path: '/reset',
        name: 'reset',
        methods: ['POST'],
    )]
    public function reset(
        UserResetPasswordResetFormRequest $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
    ): JsonResponse
    {
        $token = $request->get('token');
        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->badRequest($e->getReason());
        }

        // Encode(hash) the plain password, and set it.
        $encodedPassword = $userPasswordHasher->hashPassword(
            $user,
            $request->get('password'),
        );

        $user->setPassword($encodedPassword);
        $this->resetPasswordHelper->removeResetRequest($token);
        $userRepository->save($user, true);

        $this->cleanSessionAfterReset();

        return $this->okResponse();
    }
}
