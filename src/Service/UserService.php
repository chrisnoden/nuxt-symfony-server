<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserConfirmEmail;
use App\Message\UserWelcome;
use App\Repository\ClientRepository;
use App\Repository\UserConfirmEmailRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UserService
{
    public function __construct(
        private ClientRepository $clientRepository,
        private MailerService $mailer,
        private MessageBusInterface $bus,
        private UserConfirmEmailRepository $confirmEmailRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function updateUserData(User $user, array $requestData): User
    {
        $isNew = false;
        $uce = null;

        foreach ($requestData as $key => $val) {
            switch ($key) {
                case 'client':
                    $user->setClient($this->clientRepository->find($val['id']));
                    break;

                case 'name':
                    $user->setName($val);
                    break;

                case 'email':
                    if (null === $user->getEmail()) {
                        $user->setEmail($val);
                    } elseif ($val !== $user->getEmail()) {
                        $uce = $this->setNewUserEmail($user, $val);
                    }
                    break;

                case 'enabled':
                    $user->setEnabled($val);
                    break;

                case 'roles':
                    $user->setRoles($val);
                    break;
            }
        }

        if (null === $user->getId()) {
            $isNew = true;
        }

        $this->userRepository->save($user, true);

        if ($uce instanceof UserConfirmEmail) {
            $this->confirmEmailRepository->save($uce, true);

            $this->mailer->sendEmailAddressConfirmEmail($user, $uce->getId()->toString());
        }

        if ($isNew) {
            $this->bus->dispatch(new UserWelcome($user->getId()));
        }

        return $user;
    }

    private function setNewUserEmail(User $user, string $emailAddress): UserConfirmEmail
    {
        $this->confirmEmailRepository->expireOldRequests();

        $uce = $this->confirmEmailRepository->findOneBy(['user' => $user]);
        if ($uce instanceof UserConfirmEmail) {
            $this->confirmEmailRepository->remove($uce, true);
        }

        $uce = new UserConfirmEmail($user, $emailAddress);

        return $uce;
    }
}
