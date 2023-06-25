<?php

namespace Wexample\SymfonyHelpers\Helper;

class BundleHelper
{
    final public const AUTHOR_COMPANY = 'Wexample';
    final public const ALIAS_PREFIX = '@';
    final public const BUNDLE_PATH_RESOURCES = 'Resources'.FileHelper::FOLDER_SEPARATOR;
    final public const BUNDLE_PATH_TEMPLATES = self::BUNDLE_PATH_RESOURCES.self::DIR_TEMPLATES;
    final public const CLASS_PATH_PREFIX = ClassHelper::NAMESPACE_SEPARATOR.'App'.ClassHelper::NAMESPACE_SEPARATOR;
    final public const COMPOSER_JSON_FILE_NAME = 'composer.json';
    final public const DIR_SRC = self::FOLDER_SRC.FileHelper::FOLDER_SEPARATOR;
    final public const DIR_TEMPLATES = 'templates'.FileHelper::FOLDER_SEPARATOR;
    final public const FOLDER_SRC = 'src';
    final public const UPGRADE_TYPE_ALPHA = 'alpha';
    final public const UPGRADE_TYPE_BETA = 'beta';
    final public const UPGRADE_TYPE_DEV = 'dev';
    final public const UPGRADE_TYPE_INTERMEDIATE = 'intermediate';
    final public const UPGRADE_TYPE_MAJOR = 'major';
    final public const UPGRADE_TYPE_MINOR = 'minor';
    final public const UPGRADE_TYPE_NIGHTLY = 'nightly';
    final public const UPGRADE_TYPE_RC = 'rc';
    final public const UPGRADE_TYPE_SNAPSHOT = 'snapshot';
    final public const UPGRADE_TYPES = [
        self::UPGRADE_TYPE_ALPHA,
        self::UPGRADE_TYPE_BETA,
        self::UPGRADE_TYPE_DEV,
        self::UPGRADE_TYPE_INTERMEDIATE,
        self::UPGRADE_TYPE_MAJOR,
        self::UPGRADE_TYPE_MINOR,
        self::UPGRADE_TYPE_NIGHTLY,
        self::UPGRADE_TYPE_RC,
        self::UPGRADE_TYPE_SNAPSHOT,
    ];
    final public const VERSION_PRE_BUILD_NUMBER = 0;

    /**
     * PHP port of wex script python version.
     * Please try to maintain both versions up to date.
     *
     * @param string $version
     * @param string $upgradeType
     * @param int    $increment
     * @param bool   $build
     * @return string
     */
    public static function defaultVersionIncrement(
        string $version,
        string $upgradeType = self::UPGRADE_TYPE_MINOR,
        int $increment = 1,
        bool $build = false
    ): string {
        $timestamp = date('YmdHis');
        $preBuildNumber = self::VERSION_PRE_BUILD_NUMBER;

        // Handle 1.0.0-beta.1+build.1234
        if (str_contains($version, '-')) {
            list($baseVersion, $preBuild) = explode('-', $version);

            if (str_contains($preBuild, '.')) {
                $preBuildParts = explode('.', $preBuild);
                if (count($preBuildParts) == 2) {
                    list($preBuild, $preBuildNumber) = $preBuildParts;
                } else {
                    list($preBuild, $preBuildNumber, $_) = $preBuildParts;
                }

                $upgradeType = $preBuild;

                // preBuildNumber can be : 1+build.1234
                if (str_contains($preBuildNumber, '+')) {
                    // Ignore last part which is a timestamp.
                    $preBuildNumber = explode('+', $preBuildNumber)[0];
                }
                $preBuildNumber = (int) $preBuildNumber;
            }
        } else {
            $baseVersion = $version;
            $preBuild = '';
        }

        list($major, $intermediate, $minor) = explode('.', $baseVersion);

        // Increment according to type
        if ($upgradeType == self::UPGRADE_TYPE_MAJOR) {
            $major = strval((int) $major + $increment);
            $intermediate = $minor = '0';
        } elseif ($upgradeType == self::UPGRADE_TYPE_INTERMEDIATE) {
            $intermediate = strval((int) $intermediate + $increment);
            $minor = '0';
        } elseif (in_array($upgradeType, [
            self::UPGRADE_TYPE_ALPHA,
            self::UPGRADE_TYPE_BETA,
            self::UPGRADE_TYPE_DEV,
            self::UPGRADE_TYPE_RC,
            self::UPGRADE_TYPE_NIGHTLY,
            self::UPGRADE_TYPE_SNAPSHOT,
        ])) {
            $preBuildNumber += $increment;
        } else {
            $minor = strval((int) $minor + $increment);
        }

        // Set to zero if result is negative
        if ((int) $major < 0) {
            $major = $intermediate = $minor = '1';
        } elseif ((int) $intermediate < 0) {
            $intermediate = $minor = '0';
        } elseif ((int) $minor < 0) {
            $minor = '0';
        }

        // Build version string
        $preBuildInfo = '';
        if ($preBuild) {
            $preBuildInfo = '-'.$preBuild.'.'.$preBuildNumber;
        }

        return $major.'.'.$intermediate.'.'.$minor.$preBuildInfo.($build ? '+build.'.$timestamp : '');
    }

    public static function buildClassNameFromPackageName(string $packageName): string
    {
        [$company, $bundleName] = explode('/', $packageName);

        $company = TextHelper::toClass($company);
        $bundleName = TextHelper::toClass($bundleName);

        return implode([
            $company,
            '\\',
            $bundleName,
            '\\',
            $company,
            $bundleName,
            'Bundle',
        ]);
    }
}
