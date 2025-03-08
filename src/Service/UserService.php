<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Resource\ResponseMapper;
use App\Resource\UserRequestDto;
use App\Resource\UserResponseDto;
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

    public function create(UserRequestDto $userRequestDto): ResponseMapper
    {
        $user = $userRequestDto->toEntity();
        $user = getenv('APP_ENV') == 'test' ? $user : $this->passwordHanding($user);
        $this->repository->save($user, true);
        $token = $this->tokenManager->create($user);

        return UserResponseDto::fromEntity($user, $token);
    }

    public function update(User $user, UserRequestDto $userRequestDto): ResponseMapper
    {
        $user = $userRequestDto->updateEntity($user);

        if (!is_null($userRequestDto->pass)) {
            $user = (getenv('APP_ENV') === 'test') ? $user : $this->passwordHanding($user);
        }

        $this->entityManager->flush();
        $token = $this->tokenManager->create($user);

        return UserResponseDto::fromEntity($user, $token);
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
