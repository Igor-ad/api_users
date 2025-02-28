<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer
    ) {
    }

    protected function jsonResponse(
        mixed  $data = null,
        string $message = '',
        int    $statusCode = Response::HTTP_OK,
        array $groups = ['public'],
    ): JsonResponse {
        $response =
            [
                'statusCode' => $statusCode,
                'message' => $message,
                'data' => $data,
            ];

        $json = $this->serializer->serialize($response, 'json', ['groups' => $groups]);

        return new JsonResponse($json, $statusCode, [], true);
    }
}
