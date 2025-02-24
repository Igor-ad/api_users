<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN = [
        'login' => 'admin1',
        'phone' => '06712345',
        'pass' => '12345678',
        'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
    ];

    public const USER_1 = [
        'login' => 'user1',
        'phone' => '05098754',
        'pass' => 'user1234',
        'roles' => ['ROLE_USER'],
    ];

    public const USER_2 = [
        'login' => 'user2',
        'phone' => '06398754',
        'pass' => '12345678',
        'roles' => ['ROLE_USER'],
    ];

    public const USERS = [self::ADMIN, self::USER_1, self::USER_2];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface    $jwtManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $member) {
            $user = (new User())
                ->setLogin($member['login'])
                ->setPhone($member['phone'])
                ->setRoles($member['roles']);

            (getenv('APP_ENV') === 'test')
                ? $user->setPass($member['pass'])
                : $user->setPass($this->passwordHasher->hashPassword($user, $member['pass']));

            $manager->persist($user);
            $jwtToken = $this->jwtManager->create($user);

            echo "User created: " . $user->getUserIdentifier() . PHP_EOL;
            echo "JWT Token: " . $jwtToken . PHP_EOL;
        }
        $manager->flush();
    }
}
