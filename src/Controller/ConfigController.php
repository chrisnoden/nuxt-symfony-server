<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/config',
    name: 'api_config_',
)]
#[IsGranted('IS_AUTHENTICATED')]
class ConfigController extends AbstractController
{
    /**
     * A way to pass config data to the front-end (post login)
     */
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'env' => $_ENV['APP_ENV'],
        ]);
    }
}
