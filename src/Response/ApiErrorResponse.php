<?php

declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiErrorResponse
{
    public static function error(
        string $message,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?array $errors = null,
    ): JsonResponse {
        return new JsonResponse(
            [
                'status' => 'error',
                'code' => $code,
                'message' => $message,
                'errors' => $errors,
            ],
            $code
        );
    }
}
