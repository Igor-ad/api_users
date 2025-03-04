<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserSerializerNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    ) {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $userData = $this->normalizer->normalize($data, $format, $context);
        $token = $context['token'] ?? null;
        $resource = [
            'user' => $userData,
            'token' => $token,
        ];

        return match (true) {
            is_null($token) => ['user' => $userData],
            default => $resource,
        };
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [User::class => true];
    }
}
