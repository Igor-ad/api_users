<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        protected EntityManagerInterface      $entityManager,
        protected UserPasswordHasherInterface $passwordHasher,
        protected UserRepository              $repository,
        protected JWTTokenManagerInterface    $tokenManager,
    ) {
    }

    public function create(User $user): array
    {
        $user = getenv('APP_ENV') == 'test' ? $user : $this->passwordHanding($user);
        $user->setRoles();
        $this->repository->save($user, true);
        $token = $this->tokenManager->create($user);

        return [
            'users' => $user,
            'token' => $token
        ];
    }

    public function update(User $user, User $updatedUser): array
    {
        $user->setLogin($updatedUser->getLogin() ?? $user->getLogin());
        $user->setPhone($updatedUser->getPhone() ?? $user->getPhone());

        if (!is_null($updatedUser->getPass())) {
            $user = (getenv('APP_ENV') === 'test') ? $user : $this->passwordHanding($user);
        }

        $this->entityManager->flush();
        $token = $this->tokenManager->create($user);

        return [
            'users' => $user,
            'token' => $token
        ];
    }

    private function passwordHanding(User $user): User
    {
        $plaintextPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPass($hashedPassword);

        return $user;
    }
}
