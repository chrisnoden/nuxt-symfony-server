<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientControllerTest extends WebTestCase
{
    public function testMasterClientUserCanListClients(): void
    {
        // only a user belonging to client 1 can list clients
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve primary test user
        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('test@example.com');

        $client->loginUser($testUser);

        $client->request(
            Request::METHOD_GET,
            '/clients',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );
        /** @var Response $response */
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        // Check the returned content
        $data = json_decode($response->getContent(), true)['data'];

        $this->assertNotEmpty($data);
        $this->assertGreaterThanOrEqual(2, count($data));
        foreach ($data as $clientData) {
            $this->assertArrayHasKey('id', $clientData);
            $this->assertArrayHasKey('name', $clientData);
            $this->assertArrayHasKey('enabled', $clientData);
            $this->assertTrue($clientData['enabled']);
        }
    }

    public function testOtherUserFailsToListClients(): void
    {
        // only a user belonging to client 1 can list clients
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.does@example.com');

        $this->assertGreaterThan(1, $testUser->getClient()->getId());

        $client->loginUser($testUser);

        $client->request(
            Request::METHOD_GET,
            '/clients',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );
        /** @var Response $response */
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        // Check the returned content
        $data = json_decode($response->getContent(), true);

        $this->assertEmpty($data);
    }
}
