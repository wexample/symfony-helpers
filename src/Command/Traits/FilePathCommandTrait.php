<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Symfony\Component\HttpKernel\KernelInterface;

trait FilePathCommandTrait
{
    protected KernelInterface $kernel;

    /**
     * Get full file path from relative path
     */
    protected function getFullFilePath(string $relativePath): string
    {
        return $this->kernel->getProjectDir() . '/' . $relativePath;
    }
}
