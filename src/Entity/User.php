<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
/**
 * 'unique_login_pass' - For .env.test app with plain text passwords only.
 */
//#[ORM\UniqueConstraint(name: 'unique_login_pass', columns: ['login', 'pass'])]
class User extends AbstractUserEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * For .env.test app with plain text passwords.
     */
//    #[ORM\Column(length: 255)]
    /**
     * For an application with encrypted passwords.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    private ?string $login = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    private ?string $pass = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): static
    {
        $this->pass = $pass;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }

    public function getPassword(): ?string
    {
        return $this->getPass();
    }
}
