<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Resource\UserResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        protected EntityManagerInterface      $entityManager,
        protected UserPasswordHasherInterface $passwordHasher,
        protected UserRepository              $repository
    ) {
    }

    public function list(): array
    {
        $users = $this->repository->findAll();

        return array_map(
            fn(User $user) => UserResource::fromEntity($user)->toArray(),
            $users
        );
    }

    public function create(User $user): array
    {
        $user = getenv('APP_ENV') == 'test' ? $user : $this->passwordHanding($user);
        $user->setRoles();
        $this->repository->save($user, true);

        return UserResource::fromEntity($user)->getCreateResource();
    }

    public function update(User $user, User $updatedUser): array
    {
        $user->setLogin($updatedUser->getLogin() ?? $user->getLogin());
        $user->setPhone($updatedUser->getPhone() ?? $user->getPhone());

        if (!is_null($updatedUser->getPass())) {
            $user = (getenv('APP_ENV') === 'test') ? $user : $this->passwordHanding($user);
        }

        $this->entityManager->flush();

        return UserResource::fromEntity($updatedUser)->getUpdateResource();
    }

    public function delete(User $user): void
    {
        $this->repository->remove($user, true);
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
