<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;

abstract class AbstractRectifyRule
{
    /**
     * @return string[]
     */
    abstract public function apply(
        ReflectionClass $entityReflection
    ): array;
}
