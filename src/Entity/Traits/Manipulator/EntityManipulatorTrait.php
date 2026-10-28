<?php

namespace Wexample\SymfonyHelpers\Entity\Traits\Manipulator;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

trait EntityManipulatorTrait
{
    /**
     * The entity, or any subclass of it: what manipulates a session
     * manipulates every kind of session.
     */
    public static function manipulatesEntity(object|string $entityClass): bool
    {
        return is_a(ClassHelper::getRealClassPath($entityClass), self::getEntityClassName(), true);
    }

    /**
     * @return string|AbstractEntity
     */
    abstract public static function getEntityClassName(): string;
}
