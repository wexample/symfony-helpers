<?php

namespace Wexample\SymfonyHelpers\Entity\Traits\Manipulator;

use App\Entity\SystemParameter;

trait SystemParameterEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return SystemParameter::class;
    }
}
