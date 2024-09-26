<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Exception\UniqueConstraintViolationException;

readonly class ClientService
{
    public function __construct(
        private ClientRepository $clientRepository,
    ) {
    }

    public function updateClientData(Client $client, array $requestData): Client
    {
        foreach ($requestData as $key => $val) {
            switch ($key) {
                case 'companyName':
                    $val = trim($val);
                    $existingClient = $this->clientRepository->findOneByNameCaseInsensitive($val);
                    if ($existingClient instanceof Client && $existingClient->getId() !== $client->getId()) {
                        throw new UniqueConstraintViolationException('client name exists');
                    }

                    $client->setCompanyName($val);
                    break;

                case 'enabled':
                    $client->setEnabled($val);
                    break;
            }
        }

        $this->clientRepository->save($client, true);

        return $client;
    }
}
