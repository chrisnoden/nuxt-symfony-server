<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserConfirmEmail;
use App\FormRequest\UserConfirmEmailFormRequest;
use App\Repository\UserConfirmEmailRepository;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Somnambulist\Bundles\ApiBundle\Response\Types\ObjectType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/security/confirm-email',
    name: 'api_security_confirm-email',
    methods: ['POST']
)]
class UserConfirmEmailController extends AbstractController
{
    public function __invoke(
        UserConfirmEmailFormRequest $request,
        UserConfirmEmailRepository $confirmEmailRepository,
        UserRepository $userRepository,
    ): JsonResponse
    {
        $uce = $confirmEmailRepository->find($request->get('token'));
        if (!$uce instanceof UserConfirmEmail) {
            return $this->badRequest('invalid token');
        }

        $user = $uce->getUser();
        $user->setEmail($uce->getEmail());

        $userRepository->save($user, true);
        $confirmEmailRepository->remove($uce, true);

        return $this->item(new ObjectType($user, UserTransformer::class, ''));
    }
}
