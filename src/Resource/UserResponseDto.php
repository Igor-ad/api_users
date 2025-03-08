<?php

declare(strict_types=1);

namespace App\Resource;

use App\Entity\User;
use App\Enum\Roles;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class UserResponseDto implements ResponseMapper
{
    public function __construct(
        #[Groups(['user:detail'])]
        public ?int    $id,

        #[Groups(['user:public'])]
        public ?string $login,

        #[Groups(['user:public'])]
        public ?string $phone,

        #[Groups(['user:private'])]
        public ?string $pass,

        #[Groups(['user:detail', 'user:public'])]
        public ?string $token,

        #[Groups(['user:detail', 'user:public'])]
        public array   $roles = [Roles::User->value]
    ) {
    }

    public static function fromEntity(User $user, string $token = null): ResponseMapper
    {
        return new self(
            id: $user->getId(),
            login: $user->getLogin(),
            phone: $user->getPhone(),
            pass: $user->getPass(),
            token: $token,
            roles: $user->getRoles(),
        );
    }
}
