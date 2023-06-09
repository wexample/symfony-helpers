<?php

namespace Wexample\SymfonyHelpers\Traits;

use Wexample\SymfonyHelpers\Helper\ClassHelper;

trait EntityManipulatorTrait
{
    public static function manipulatesEntity(object|string $entityClass): bool
    {
        return ClassHelper::isClassPath($entityClass, self::getEntityClassName());
    }

    abstract public static function getEntityClassName(): string;
}
