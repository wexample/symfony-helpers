<?php

namespace Wexample\SymfonyHelpers\Helper;

use function realpath;
use function strlen;
use function substr;

class PathHelper
{
    public static function relativeTo(
        string $path,
        string $basePath
    ): string {
        return substr(
            $path,
            strlen(
                realpath(
                    $basePath
                ).'/'
            )
        );
    }
}
