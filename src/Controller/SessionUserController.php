<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\FormRequest\SessionUserDataFormRequest;
use App\Service\SessionUserService;
use App\Transformer\UserTransformer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Somnambulist\Bundles\ApiBundle\Response\Types\ObjectType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class SessionUserController extends AbstractController
{
    #[Route(
        path: '/session/me',
        name: 'api_session_me',
        methods: ['GET']
    )]
    public function currentUser(#[CurrentUser] ?User $user): JsonResponse
    {
        return $this->item(new ObjectType($user, UserTransformer::class, ''));
    }

    #[Route(
        path: '/session/me',
        name: 'api_session_me_update',
        methods: ['POST']
    )]
    public function updateCurrentUserDetails(
        SessionUserDataFormRequest $request,
        SessionUserService $userService,
        UserPasswordHasherInterface $passwordHasher,
        #[CurrentUser] User $user
    ): JsonResponse
    {
        // password is used to authenticate changes to the logged in user
        if (!$passwordHasher->isPasswordValid($user, $request->get('password'))) {
            return $this->unauthorizedResponse();
        }

        $userService->updateUserData($user, $request->data()->all());

        return $this->item(new ObjectType($user, UserTransformer::class, ''));
    }
}
