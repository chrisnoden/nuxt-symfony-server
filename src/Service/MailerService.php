<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class MailerService implements AuthCodeMailerInterface
{
    public function __construct(
        private string $fromAddressEmail,
        private string $fromAddressName,
        private string $frontEndHostname,
        private string $siteName,
        private MailerInterface $mailer,
    ) {
    }

    private function fromAddress(): Address
    {
        return new Address($this->fromAddressEmail, $this->fromAddressName);
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $mfaCode = $user->getEmailAuthCode();
        $email = (new TemplatedEmail())
            ->from($this->fromAddress())
            ->to($user->getEmailAuthRecipient())
            ->subject($this->siteName . ' > Confirm your login request')
            ->htmlTemplate('email/email-2fa-response.html.twig')
            ->context(
                [
                    'frontend' => $this->frontEndHostname,
                    'siteName' => $this->siteName,
                    'mfaCode'  => $mfaCode,
                ]
            )
        ;

        $this->mailer->send($email);
    }

    public function sendEmailAddressConfirmEmail(User $user, string $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromAddress())
            ->to($user->getEmail())
            ->subject($this->siteName. ' > Confirm your email address')
            ->htmlTemplate('email/confirm_email_address.html.twig')
            ->context(
                [
                    'frontend' => $this->frontEndHostname,
                    'user'     => $user,
                    'siteName' => $this->siteName,
                    'token'    => $resetToken,
                ]
            )
        ;

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromAddress())
            ->to($user->getEmail())
            ->subject($this->siteName. ' > Your password reset request')
            ->htmlTemplate('email/reset_password.html.twig')
            ->context(
                [
                    'frontend' => $this->frontEndHostname,
                    'user'     => $user,
                    'siteName' => $this->siteName,
                    'token'    => $token,
                ]
            )
        ;

        $this->mailer->send($email);
    }

    public function sendNewUserWelcomeEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromAddress())
            ->to($user->getEmail())
            ->subject($this->siteName. ' > Activate your new user login')
            ->htmlTemplate('email/new_user_welcome.html.twig')
            ->context(
                [
                    'frontend' => $this->frontEndHostname,
                    'user'     => $user,
                    'siteName' => $this->siteName,
                    'token'    => $token,
                ]
            )
        ;

        $this->mailer->send($email);
    }
}
