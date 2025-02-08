<?php

namespace Wexample\SymfonyHelpers\Entity\Traits\Manipulator;

use Wexample\Helpers\Helper\ClassHelper;

trait EntityManipulatorTrait
{
    public static function manipulatesEntity(object|string $entityClass): bool
    {
        return ClassHelper::isClassPath($entityClass, self::getEntityClassName());
    }

    abstract public static function getEntityClassName(): string;
}
