<?php

namespace Wexample\SymfonyHelpers\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Helper\BundleHelper;

class BundleService
{
    public function __construct(
        protected KernelInterface $kernel,
    ) {

    }

    /**
     * Increment every package and update dependencies.
     * @return array
     */
    public function updateAllLocalPackages(): array
    {
        $paths = $this->getAllLocalPackagesPaths();
        $output = [];

        foreach ($paths as $path) {
            if ($newVersion = $this->versionBuild($path)) {
                $output[BundleHelper::getPackageComposerConfiguration($path)->name] = $newVersion;
            }
        }

        $this->updateAllRequirementsVersions();

        return $output;
    }

    public function versionBuild(
        string $packagePath,
        string $upgradeType = BundleHelper::UPGRADE_TYPE_MINOR,
        int $increment = 1,
        bool $build = false,
        string $version = null
    ): string {
        $config = BundleHelper::getPackageComposerConfiguration($packagePath);

        if (!$version) {
            $version = $config->version;
        }

        // Version increment
        $config->version = BundleHelper::defaultVersionIncrement(
            $version,
            $upgradeType,
            $increment,
            $build
        );

        BundleHelper::savePackageComposerConfiguration(
            $packagePath,
            $config
        );

        return $config->version;
    }

    public function updateAllRequirementsVersions(): array
    {
        $packages = $this->getAllLocalPackagesPaths();
        $updated = [];

        foreach ($packages as $packagePath) {
            $updated += $this->updateRequirementVersion(
                $packagePath,
            );
        }

        return $updated;
    }

    public function getAllLocalPackagesPaths(): array
    {
        $vendorsDir = $this->kernel->getProjectDir() . '/vendor-local/';
        $finder = new Finder();

        $finder->directories()->in($vendorsDir)->depth('== 1');

        $packages = [];
        foreach ($finder as $file) {
            $path = $file->getRealPath().'/';
            $packages[BundleHelper::getPackageComposerConfiguration($path)->name] = $path;
        }

        return $packages;
    }

    public function updateRequirementVersion(string $packagePath): array
    {
        $packages = $this->getAllLocalPackagesPaths();
        $config = BundleHelper::getPackageComposerConfiguration($packagePath);
        $packageName = $config->name;
        $updated = [];

        foreach ($packages as $packageNameDest => $packageDestPath) {
            if ($packageNameDest !== $packageName) {
                $configDest = BundleHelper::getPackageComposerConfiguration($packageDestPath);
                $changed = false;
                $newVersion = '^'.$config->version;

                if (isset($configDest->require->$packageName)
                    && $configDest->require->$packageName != $newVersion) {
                    $changed = true;
                    $configDest->require->$packageName = $newVersion;
                }

                $requireDevKey = 'require-dev';
                if (isset($configDest->$requireDevKey->$packageName)
                    && $configDest->$requireDevKey->$packageName != $newVersion) {
                    $changed = true;
                    $configDest->require->$packageName = $newVersion;
                }

                if ($changed) {
                    BundleHelper::savePackageComposerConfiguration(
                        $packageDestPath,
                        $configDest
                    );

                    $updated[$configDest->name] = $packageDestPath;
                }
            }
        }

        return $updated;
    }
}
