<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testUserCanOnlyListTheirOwnUsersByDefault(): void
    {
        // client 2 user should only have other client 2 users in their list
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.does@example.com');

        $client->loginUser($testUser);
        $this->assertSame(2, $testUser->getClient()->getId());

        $client->request(
            Request::METHOD_GET,
            '/users',
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
        $this->assertCount(32, $data);
        foreach ($data as $clientData) {
            $this->assertArrayHasKey('id', $clientData);
            $this->assertArrayHasKey('client', $clientData);
            $this->assertArrayHasKey('name', $clientData);
            $this->assertArrayHasKey('email', $clientData);
            $this->assertArrayHasKey('roles', $clientData);
            $this->assertArrayHasKey('enabled', $clientData);
            $this->assertArrayHasKey('twoFactorEnabled', $clientData);

            $this->assertSame($testUser->getClient()->getCompanyName(), $clientData['client']['name']);
        }
    }

    public function testUserCannotQueryOtherClientUsers(): void
    {
        // client 2 user should only have other client 2 users in their list - even if they filter by client
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.does@example.com');

        $client->loginUser($testUser);
        $this->assertSame(2, $testUser->getClient()->getId());

        $client->request(
            Request::METHOD_GET,
            '/users',
            [
                'client' => 1,
            ],
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
        $this->assertCount(32, $data);
        foreach ($data as $clientData) {
            $this->assertArrayHasKey('id', $clientData);
            $this->assertArrayHasKey('client', $clientData);
            $this->assertArrayHasKey('name', $clientData);
            $this->assertArrayHasKey('email', $clientData);
            $this->assertArrayHasKey('roles', $clientData);
            $this->assertArrayHasKey('enabled', $clientData);
            $this->assertArrayHasKey('twoFactorEnabled', $clientData);

            $this->assertSame($testUser->getClient()->getCompanyName(), $clientData['client']['name']);
        }
    }

    public function testMasterClientUserCanListAllUsers(): void
    {
        // client 1 user should only be able to list all users
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('test@example.com');

        $client->loginUser($testUser);
        $this->assertSame(1, $testUser->getClient()->getId());

        $client->request(
            Request::METHOD_GET,
            '/users',
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
        $this->assertCount(33, $data);
        foreach ($data as $clientData) {
            $this->assertArrayHasKey('id', $clientData);
            $this->assertArrayHasKey('client', $clientData);
            $this->assertArrayHasKey('name', $clientData);
            $this->assertArrayHasKey('email', $clientData);
            $this->assertArrayHasKey('roles', $clientData);
            $this->assertArrayHasKey('enabled', $clientData);
            $this->assertArrayHasKey('twoFactorEnabled', $clientData);
        }
    }

    public function testMasterClientUserCanFilterUsersByClient(): void
    {
        // client 1 user should only be able to list all users
        $client         = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('test@example.com');

        $client->loginUser($testUser);
        $this->assertSame(1, $testUser->getClient()->getId());

        $client->request(
            Request::METHOD_GET,
            '/users',
            [
                'client' => 2,
            ],
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
        $this->assertCount(32, $data);
        foreach ($data as $clientData) {
            $this->assertArrayHasKey('id', $clientData);
            $this->assertArrayHasKey('client', $clientData);
            $this->assertArrayHasKey('name', $clientData);
            $this->assertArrayHasKey('email', $clientData);
            $this->assertArrayHasKey('roles', $clientData);
            $this->assertArrayHasKey('enabled', $clientData);
            $this->assertArrayHasKey('twoFactorEnabled', $clientData);

            $this->assertSame('Another Company', $clientData['client']['name']);
        }
    }
}
