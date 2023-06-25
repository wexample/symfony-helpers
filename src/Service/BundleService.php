<?php

namespace Wexample\SymfonyHelpers\Service;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\JsonHelper;

class BundleService
{
    public function __construct(
        protected KernelInterface $kernel,
    ) {

    }

    public function getBundleIfExists(BundleInterface|string $bundleClass): ?BundleInterface
    {
        try {
            return $this->getBundle(
                $bundleClass
            );
        } catch (\Exception) {

        }

        return null;
    }

    /**
     * @param BundleInterface|string $bundleIdentifier Can be a package directory, a bundle/short-name, a BundleShortNameBundle or a full bundle classname.
     * @return BundleInterface
     */
    public function getBundle(BundleInterface|string $bundleIdentifier): BundleInterface
    {
        if (is_string($bundleIdentifier)) {
            if (is_dir($bundleIdentifier)) {
                $bundleIdentifier = BundleHelper::buildClassNameFromPackageName(
                    $this->getPackageComposerConfiguration($bundleIdentifier)->name
                );
            } elseif (count(explode('/', $bundleIdentifier)) === 2) {
                $bundleIdentifier = BundleHelper::buildClassNameFromPackageName(
                    $bundleIdentifier
                );
            }

            if (class_exists($bundleIdentifier)) {
                $bundleIdentifier = ClassHelper::getShortName(
                    $bundleIdentifier
                );
            }

            return $this->kernel->getBundle($bundleIdentifier);
        }

        return $bundleIdentifier;
    }

    public function getPackageComposerConfiguration(string $packagePath): object
    {
        return JsonHelper::read(
            $packagePath.BundleHelper::COMPOSER_JSON_FILE_NAME
        );
    }

    public function getBundleComposerConfiguration(BundleInterface|string $bundle): object
    {
        return $this->getPackageComposerConfiguration(
            $this->getBundleRootPath($bundle)
        );
    }

    public function getBundleRootPath(BundleInterface|string $bundle): string
    {
        return realpath($this->getBundle($bundle)->getPath().'/../').'/';
    }

    public function savePackageComposerConfiguration(
        string $packagePath,
        object $config
    ): bool {
        return JsonHelper::write(
            $packagePath.'composer.json',
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
