<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    protected array $roles = ['ROLE_USER'];

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles = ['ROLE_USER']): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }
}
