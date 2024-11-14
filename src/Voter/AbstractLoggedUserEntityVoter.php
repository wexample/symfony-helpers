<?php

namespace Wexample\SymfonyHelpers\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Wexample\SymfonyHelpers\Entity\AbstractUser;

abstract class AbstractLoggedUserEntityVoter extends AbstractEntityVoter
{
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof AbstractUser) {
            // the user must be logged in; if not, deny access
            return false;
        }

        return true;
    }
}
