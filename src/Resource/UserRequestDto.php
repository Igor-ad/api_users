<?php

declare(strict_types=1);

namespace App\Resource;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRequestDto
{
    public function __construct(
        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Length(min: 3)]
        #[Assert\Length(max: 8)]
        public ?string $login,

        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Length(min: 3)]
        #[Assert\Length(max: 8)]
        public ?string $phone,

        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Length(min: 3)]
        #[Assert\Length(max: 8)]
        public ?string $pass,
    ) {}

    public function toEntity(): User
    {
        return (new User())
            ->setLogin($this->login)
            ->setPhone($this->phone)
            ->setPass($this->pass);
    }

    public function updateEntity(User $user): User
    {
        return $user
            ->setLogin($this->login ?? $user->getLogin())
            ->setPhone($this->phone ?? $user->getPhone())
            ->setPass($this->pass ?? $user->getPass());
    }
}
