<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

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
    final public const DIR_TEMPLATE_PAGES = 'pages'.FileHelper::FOLDER_SEPARATOR;
    final public const DIR_TESTS = 'tests'.FileHelper::FOLDER_SEPARATOR;
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
                if (2 == count($preBuildParts)) {
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
        if (self::UPGRADE_TYPE_MAJOR == $upgradeType) {
            $major = strval((int) $major + $increment);
            $intermediate = $minor = '0';
        } elseif (self::UPGRADE_TYPE_INTERMEDIATE == $upgradeType) {
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

    public static function savePackageComposerConfiguration(
        string $packagePath,
        object $config
    ): bool {
        return JsonHelper::write(
            $packagePath.'composer.json',
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    public function getBundleComposerConfiguration(
        BundleInterface|string $bundle,
        KernelInterface $kernel,
    ): object {
        return PackageHelper::getPackageComposerConfiguration(
            self::getBundleRootPath($bundle, $kernel)
        );
    }

    public static function getBundleRootPath(
        BundleInterface|string $bundle,
        KernelInterface $kernel
    ): string {
        return realpath(BundleHelper::getBundle($bundle, $kernel)->getPath().'/../').'/';
    }

    public static function getBundleCssAlias(string $className): string
    {
        return self::ALIAS_PREFIX.self::getBundlePackageNameFromClassName($className);
    }

    public static function getBundlePackageNameFromClassName(string $className): string
    {
        $parts = array_map([TextHelper::class, 'toKebab'], explode('\\', $className));

        return $parts[0].'/'.$parts[1];
    }

    /**
     * @param BundleInterface|string $bundleIdentifier can be a package directory, a bundle/short-name, a BundleShortNameBundle or a full bundle classname
     */
    public static function getBundle(
        BundleInterface|string $bundleIdentifier,
        KernelInterface $kernel
    ): ?BundleInterface {
        if (is_string($bundleIdentifier)) {
            if (is_dir($bundleIdentifier)) {
                $bundleIdentifier = BundleHelper::buildClassNameFromPackageName(
                    PackageHelper::getPackageComposerConfiguration($bundleIdentifier)->name
                );
            } elseif (2 === count(explode('/', $bundleIdentifier))) {
                $bundleIdentifier = BundleHelper::buildClassNameFromPackageName(
                    $bundleIdentifier
                );
            }

            if (class_exists($bundleIdentifier)) {
                $bundleIdentifier = ClassHelper::getShortName(
                    $bundleIdentifier
                );
            }

            return $kernel->getBundle($bundleIdentifier);
        }

        return $bundleIdentifier;
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
