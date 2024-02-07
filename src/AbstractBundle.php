<?php

namespace Wexample\SymfonyHelpers;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\FileHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;

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
