<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Behaviours\RequestCriteria;
use App\Entity\User;
use App\FormRequest\UserSearchFormRequest;
use App\FormRequest\UserSetDataFormRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Transformer\UserTransformer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Somnambulist\Bundles\ApiBundle\Response\Types\ObjectType;
use Somnambulist\Bundles\ApiBundle\Response\Types\PagerfantaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/user',
    name: 'api_user_',
)]
#[IsGranted('ROLE_USER_ADMINISTRATION')]
class UserController extends AbstractController
{
    use RequestCriteria;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(
        path: 's',
        name: 'search',
        methods: ['GET']
    )]
    public function search(
        UserSearchFormRequest $request,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $results = $this->userRepository->search(
            $user->getClient(),
            $this->requestCriteria($request),
            $request->orderBy(),
            $request->page(),
            $request->perPage()
        );

        return $this->paginate(
            PagerfantaType::fromFormRequest($request, $results, UserTransformer::class)
        );
    }

    #[Route(
        path: '/{userId}',
        name: 'find',
        requirements: [
            'userId' => '%route.requirements.uuid%',
        ],
        methods: ['GET']
    )]
    public function find(
        string $userId,
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        $result = $this->userRepository->find($userId);

        if ($user->getClient()->getId() > 1 && $user->getClient()->getId() !== $result->getClient()->getId()) {
            return $this->badRequest();
        }

        if (!$result instanceof User) {
            return $this->notFoundResponse();
        }

        return $this->item(new ObjectType($result, UserTransformer::class, key: ''));
    }

    #[Route(
        path: '/{userId}',
        name: 'update',
        requirements: [
            'userId' => '%route.requirements.uuid%',
        ],
        methods: ['POST']
    )]
    public function update(
        string $userId,
        UserSetDataFormRequest $request,
        UserService $userService,
        #[CurrentUser] User $sessionUser,
    ): JsonResponse
    {
        $user = $this->userRepository->find($userId);

        if ($sessionUser->getClient()->getId() > 1 && $sessionUser->getClient()->getId() !== $user->getClient()->getId()) {
            return $this->notFoundResponse();
        }

        $userService->updateUserData($user, $request->all());

        return $this->item(new ObjectType($user, UserTransformer::class, key: ''));
    }

    #[Route(
        path: '',
        name: 'create',
        methods: ['PUT']
    )]
    public function create(
        UserSetDataFormRequest $request,
        UserService $userService,
        #[CurrentUser] User $sessionUser,
    ): JsonResponse
    {
        $chosenClient = $request->get('client');
        if ($sessionUser->getClient()->getId() > 1 && $sessionUser->getClient()->getId() !== $chosenClient['id']) {
            return $this->badRequest('unable to set different client');
        }

        $user = (new User());

        try {
            $userService->updateUserData($user, $request->all());
        } catch (UniqueConstraintViolationException) {
            return $this->badRequest('email address already registered');
        }

        return $this->item(new ObjectType($user, UserTransformer::class, key: ''));
    }
}
