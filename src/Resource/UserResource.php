<?php

declare(strict_types=1);

namespace App\Resource;

use App\Entity\User;

/**
 * The "pass" field contains sensitive data.
 */
readonly class UserResource
{
    public function __construct(
        public ?int    $id,
        public ?string $login,
        public ?string $phone,
        public ?string $pass,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId(),
            login: $user->getLogin(),
            phone: $user->getPhone(),
            pass: $user->getPass(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'phone' => $this->phone,
        ];
    }

    public function getCreateResource(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'phone' => $this->phone,
            'pass' => (getenv('INCLUDE_CONFIDENTIAL_FIELDS') === 'true') ? $this->pass : '***'
        ];
    }

    public function getShowResource(): array
    {
        return [
            'login' => $this->login,
            'phone' => $this->phone,
            'pass' => (getenv('INCLUDE_CONFIDENTIAL_FIELDS') === 'true') ? $this->pass : '***'
        ];
    }

    public function getUpdateResource(): array
    {
        return [
            'id' => $this->id
        ];
    }
}
