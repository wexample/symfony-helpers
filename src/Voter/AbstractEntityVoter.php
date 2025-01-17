<?php

namespace Wexample\SymfonyHelpers\Voter;

use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractEntityVoter extends AbstractVoter
{
    use EntityManipulatorTrait;

    final public const string EDIT = VariableHelper::EDIT;
    final public const string VIEW = VariableHelper::VIEW;

    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        if (parent::supports($attribute, $subject)) {
            if (!$this->manipulatesEntity($subject)) {
                return false;
            }

            return true;
        }

        return false;
    }

    protected function getAllowedAttributes(): array
    {
        return [
            static::EDIT,
            static::VIEW,
        ];
    }
}
