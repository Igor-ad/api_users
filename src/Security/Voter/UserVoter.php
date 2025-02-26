<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Enum\Roles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$this->supports($attribute, $subject)) {
            return false;
        }

        return match (true) {
            (in_array(Roles::Admin->value, $user->getRoles())),
            ($attribute === self::CREATE) => true,
            ($attribute === self::EDIT),
            ($attribute === self::VIEW) => ($user->getId() === $subject->getId()),
            default => false
        };
    }
}
