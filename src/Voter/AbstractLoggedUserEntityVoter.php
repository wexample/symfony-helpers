<?php

namespace Wexample\SymfonyHelpers\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Wexample\SymfonyHelpers\Entity\AbstractUser;
use Wexample\SymfonyHelpers\Helper\RoleHelper;

abstract class AbstractLoggedUserEntityVoter extends AbstractEntityVoter
{
    protected bool $onlyAdmin = false;

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool
    {
        $user = $token->getUser();

        if (!$user instanceof AbstractUser) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // Check if user has any of the allowed roles
        foreach ($this->getAllowedRoles(
            attribute: $attribute,
            subject: $subject,
            token: $token,
        ) as $allowedRole) {
            if (in_array($allowedRole, $user->getRoles(), true)) {
                return true;
            }
        }

        // If none of the allowed roles match, deny access
        return false;
    }

    protected function getAllowedRoles(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): array
    {
        if ($this->onlyAdmin) {
            return [RoleHelper::ROLE_ADMIN];
        }

        return [RoleHelper::ROLE_USER];
    }
}
