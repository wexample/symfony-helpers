<?php

namespace Wexample\SymfonyHelpers\Class;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\FileHelper;

abstract class AbstractBundle extends Bundle
{
    public static function getTemplatePath(string $path): string
    {
        return BundleHelper::ALIAS_PREFIX
            .static::getAlias()
            .FileHelper::FOLDER_SEPARATOR
            .$path;
    }

    public static function getAlias(): string
    {
        return ClassHelper::getShortName(static::class);
    }
}
