<?php

namespace Wexample\SymfonyHelpers\Service\Entity;

use Wexample\SymfonyHelpers\Traits\EntityManipulatorTrait;

abstract class AbstractEntityService extends EntityNeutralService
{
    use EntityManipulatorTrait;
}