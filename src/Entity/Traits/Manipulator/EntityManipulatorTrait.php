<?php

namespace Wexample\SymfonyHelpers\Entity\Traits\Manipulator;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

trait EntityManipulatorTrait
{
    public static function manipulatesEntity(object|string $entityClass): bool
    {
        return ClassHelper::isClassPath($entityClass, self::getEntityClassName());
    }

    /**
     * @return string|AbstractEntity
     */
    abstract public static function getEntityClassName(): string;
}
