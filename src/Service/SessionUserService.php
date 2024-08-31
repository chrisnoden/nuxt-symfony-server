<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserConfirmEmail;
use App\Repository\UserConfirmEmailRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class SessionUserService
{
    public function __construct(
        private MailerService $mailer,
        private UserConfirmEmailRepository $confirmEmailRepository,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function updateUserData(User $user, array $requestData): User
    {
        foreach ($requestData as $key => $val) {
            switch ($key) {
                case 'name':
                    $user->setName($val);
                    break;

                case 'email':
                    $this->setNewUserEmail($user, $val);
                    break;

                case 'newPassword':
                    // Encode(hash) the plain password, and set it.
                    $encodedPassword = $this->userPasswordHasher->hashPassword(
                        $user,
                        $val,
                    );

                    $user->setPassword($encodedPassword);
                    break;
            }
        }

        $this->userRepository->save($user, true);

        return $user;
    }

    private function setNewUserEmail(User $user, string $emailAddress): void
    {
        $this->confirmEmailRepository->expireOldRequests();

        $uce = $this->confirmEmailRepository->findOneBy(['user' => $user]);
        if ($uce instanceof UserConfirmEmail) {
            $uce->resetExpiry();
        } else {
            $uce = new UserConfirmEmail($user, $emailAddress);
        }

        $this->confirmEmailRepository->save($uce, true);

        $this->mailer->sendEmailAddressConfirmEmail($user, $uce->getId()->toString());
    }
}
