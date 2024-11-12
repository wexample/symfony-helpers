<?php

namespace Wexample\SymfonyHelpers\Controller\Traits;

use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait EntityControllerTrait
{
    use EntityManipulatorTrait;

    protected function getControllerEntityClassName(): string
    {
        return static::getEntityClassName();
    }
}