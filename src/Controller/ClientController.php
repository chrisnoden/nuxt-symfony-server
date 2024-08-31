<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Behaviours\RequestCriteria;
use App\Entity\User;
use App\FormRequest\ClientSearchFormRequest;
use App\Repository\ClientRepository;
use App\Transformer\ClientTransformer;
use Somnambulist\Bundles\ApiBundle\Response\Types\PagerfantaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/client',
    name: 'api_client_',
)]
#[IsGranted('ROLE_CLIENT_ADMINISTRATION')]
class ClientController extends AbstractController
{
    use RequestCriteria;

    #[Route(
        path: 's',
        name: 'search',
        methods: ['GET']
    )]
    public function search(
        ClientSearchFormRequest $request,
        ClientRepository $clientRepository,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        if ($user->getClient()->getId() !== 1) {
            return $this->unauthorizedResponse();
        }

        $results = $clientRepository->search(
            $this->requestCriteria($request),
            $request->orderBy(),
            $request->page(),
            $request->perPage()
        );

        return $this->paginate(
            PagerfantaType::fromFormRequest($request, $results, ClientTransformer::class)
        );
    }
}
