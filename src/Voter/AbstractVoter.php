<?php

namespace Wexample\SymfonyHelpers\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, $this->getAllowedAttributes())) {
            return false;
        }

        return true;
    }

    abstract protected function getAllowedAttributes(): array;
}
