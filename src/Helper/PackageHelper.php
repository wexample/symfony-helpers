<?php

namespace Wexample\SymfonyHelpers\Helper;

class PackageHelper
{
    public static function lastCommitHasVersionTag(string $path): bool
    {
        return in_array(
            'v' . self::getPackageComposerConfiguration($path)->version,
            GitHelper::getTagsForLastCommit($path)
        );
    }

    public static function getPackageComposerConfiguration(string $packagePath): object
    {
        return JsonHelper::read(
            $packagePath.BundleHelper::COMPOSER_JSON_FILE_NAME
        );
    }
}
