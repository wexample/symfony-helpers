<?php

namespace Wexample\SymfonyHelpers\Service;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
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

    public function getBundle(BundleInterface|string $bundleClass): BundleInterface
    {
        if (is_string($bundleClass)) {
            return $this->kernel->getBundle(
                ClassHelper::getShortName(
                    $bundleClass
                )
            );
        }

        return $bundleClass;
    }

    public function getBundleComposerConfiguration(BundleInterface|string $bundle): object
    {
        return $this->getPackageComposerConfiguration(
            $this->getBundleRootPath($bundle)
        );
    }

    public function getPackageComposerConfiguration(string $packagePath): object
    {
        return JsonHelper::read(
            $packagePath.'composer.json'
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
