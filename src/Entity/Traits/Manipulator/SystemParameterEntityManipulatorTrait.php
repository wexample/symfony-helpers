<?php

namespace Wexample\SymfonyHelpers\Entity\Traits\Manipulator;

use Wexample\SymfonyHelpers\Entity\SystemParameter;

trait SystemParameterEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return SystemParameter::class;
    }
}
