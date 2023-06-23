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
        return JsonHelper::read(
            $this->getBundleRootPath($bundle).'composer.json'
        );
    }

    public function getBundleRootPath(BundleInterface|string $bundle): string
    {
        return realpath($this->getBundle($bundle)->getPath().'/../').'/';
    }

    public function saveBundleComposerConfiguration(
        BundleInterface|string $bundle,
        object $config
    ): bool {
        return JsonHelper::write(
            $this->getBundleRootPath($bundle).'composer.json',
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
