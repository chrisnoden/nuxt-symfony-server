<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\UserWelcome;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[AsMessageHandler]
readonly class UserWelcomeHandler
{

    public function __construct(
        private MailerService $mailer,
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(
        UserWelcome $message,
    ) {
        $user = $this->userRepository->find($message->getUserId());

        if ($user instanceof User) {
            try {
                $token = $this->resetPasswordHelper->generateResetToken($user);
                $this->mailer->sendNewUserWelcomeEmail($user, $token->getToken());
            } catch (\Exception $e) {
                throw new RecoverableMessageHandlingException($e->getMessage());
            }
        }
    }
}
