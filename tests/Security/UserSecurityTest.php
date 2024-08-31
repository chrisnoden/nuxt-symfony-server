<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Support\Behaviours\BootTestClient;
use App\Tests\Support\Behaviours\MakeJsonRequestTo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSecurityTest extends WebTestCase
{
    use BootTestClient;
    use MakeJsonRequestTo;

    public function testCanLoginEnabledUserWithoutTwoFactor(): void
    {
        $client         = $this->__kernelBrowserClient;
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve an enabled test user
        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.does@example.com');

        $client->request(
            Request::METHOD_POST,
            '/security/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email'    => $testUser->getEmail(),
                'password' => 'vafnLPiH.8@g',
            ])
        );
        /** @var Response $response */
        $response = $client->getResponse();

        // Check the returned content
        $data = json_decode($response->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('login', $data);
        $this->assertArrayHasKey('two_factor_required', $data);
        $this->assertSame('success', $data['login']);
        $this->assertSame(false, $data['two_factor_required']);

        // Check the cookies
        $cookies               = $response->headers->getCookies();
        $foundRememberMeCookie = false;
        /** @var Cookie $cookie */
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'REMEMBERME') {
                $foundRememberMeCookie = true;
                $this->assertNull($cookie->getValue(), 'REMEMBERME cookie value should be null');
            }
        }

        $this->assertTrue($foundRememberMeCookie, 'REMEMBERME cookie not set');
    }

    public function testCanLoginAndRememberEnabledUserWithoutTwoFactor(): void
    {
        $client         = $this->__kernelBrowserClient;
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve an enabled test user
        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.does@example.com');

        $client->request(
            Request::METHOD_POST,
            '/security/login',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'email'        => $testUser->getEmail(),
                'password'     => 'vafnLPiH.8@g',
                '_remember_me' => true,
            ])
        );
        /** @var Response $response */
        $response = $client->getResponse();

        // Check the returned content
        $data = json_decode($response->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('login', $data);
        $this->assertArrayHasKey('two_factor_required', $data);
        $this->assertSame('success', $data['login']);
        $this->assertSame(false, $data['two_factor_required']);

        // Check the cookies
        $cookies               = $response->headers->getCookies();
        $foundRememberMeCookie = false;
        /** @var Cookie $cookie */
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'REMEMBERME') {
                $foundRememberMeCookie = true;
                $this->assertNotNull($cookie->getValue(), 'REMEMBERME cookie value should not be null');
            }
        }

        $this->assertTrue($foundRememberMeCookie, 'REMEMBERME cookie not set');
    }

    public function testFailLoginWithDisabledUserWithoutTwoFactor(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve an enabled test user
        /** @var User $testUser */
        $testUser = $userRepository->findOneByEmail('john.dont@example.com');

        // Test login with email and password
        $response = $this->makeJsonRequestTo(
            '/security/login',
            method: Request::METHOD_POST,
            payload: [
                'json' => [
                    'email'    => $testUser->getEmail(),
                    'password' => 'vafnLPiH.8@g',
                ]
            ],
            expectedStatusCode: Response::HTTP_UNAUTHORIZED,
        );

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertSame('Your user account has been disabled', $response['error']);
    }
}
