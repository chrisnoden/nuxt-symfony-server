<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Behaviours\RequestCriteria;
use App\Entity\Client;
use App\Entity\User;
use App\Exception\UniqueConstraintViolationException;
use App\FormRequest\ClientSearchFormRequest;
use App\FormRequest\ClientSetDataFormRequest;
use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Transformer\ClientTransformer;
use Somnambulist\Bundles\ApiBundle\Response\Types\ObjectType;
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

    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }

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

    #[Route(
        path: '/{clientId}',
        name: 'find',
        requirements: [
            'clientId' => '%route.requirements.id%',
        ],
        methods: ['GET']
    )]
    public function find(
        string $clientId,
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        if ($user->getClient()->getId() > 1) {
            return $this->unauthorizedResponse();
        }

        $client = $this->clientRepository->find($clientId);

        if (!$client instanceof Client) {
            return $this->notFoundResponse();
        }

        return $this->item(new ObjectType($client, ClientTransformer::class, key: ''));
    }

    #[Route(
        path: '/{clientId}',
        name: 'update',
        requirements: [
            'clientId' => '%route.requirements.id%',
        ],
        methods: ['POST']
    )]
    public function update(
        string $clientId,
        ClientSetDataFormRequest $request,
        ClientService $clientService,
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        if ($user->getClient()->getId() > 1) {
            return $this->unauthorizedResponse();
        }

        $client = $this->clientRepository->find($clientId);

        if (!$client instanceof Client) {
            return $this->notFoundResponse();
        }

        $clientService->updateClientData($client, $request->all());

        return $this->item(new ObjectType($client, ClientTransformer::class, key: ''));
    }

    #[Route(
        path: '',
        name: 'create',
        methods: ['PUT']
    )]
    public function create(
        ClientSetDataFormRequest $request,
        ClientService $clientService,
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        if ($user->getClient()->getId() > 1) {
            return $this->unauthorizedResponse();
        }

        $client = (new Client());

        try {
            $clientService->updateClientData($client, $request->all());
        } catch (UniqueConstraintViolationException $e) {
            return $this->badRequest($e->getMessage());
        }

        return $this->item(new ObjectType($client, ClientTransformer::class, key: ''));
    }
}
