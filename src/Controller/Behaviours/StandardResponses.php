<?php

declare(strict_types=1);

namespace App\Controller\Behaviours;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait StandardResponses
{
    protected function badRequest(?string $msg = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $msg ?? 'Bad Request',
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function deletedResponse(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Deleted',
        ], Response::HTTP_NO_CONTENT);
    }

    protected function notFoundResponse(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Not found',
        ], Response::HTTP_NOT_FOUND);
    }

    protected function okResponse(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'OK',
        ], Response::HTTP_OK);
    }

    protected function unexpectedErrorResponse(?string $msg = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $msg ?? 'Unexpected server error',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function unauthorizedResponse(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Unauthorized',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
