<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use Wexample\SymfonyHelpers\Helper\TextHelper;

trait HasSnakeShortClassNameClassTrait
{
    use HasShortClassNameClassTrait;

    public static function getSnakeShortClassName(): string
    {
        return TextHelper::toSnake(static::getShortClassName());
    }
}