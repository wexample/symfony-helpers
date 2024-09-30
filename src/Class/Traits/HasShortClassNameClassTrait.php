<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;

trait HasShortClassNameClassTrait
{
    protected static function getClassNameSuffix(): ?string
    {
        return null;
    }

    public static function getShortClassName(): string
    {
        $shortName = ClassHelper::getShortName(static::class);

        if ($suffix = self::getClassNameSuffix()) {
            return TextHelper::removeSuffix($shortName, $suffix);
        }

        return $shortName;
    }
}