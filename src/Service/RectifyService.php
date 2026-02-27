<?php

namespace Wexample\SymfonyHelpers\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Attribute\RectifiableEntity;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class RectifyService
{
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    /**
     * @return string[]
     */
    public function validateRectifiableEntities(): array
    {
        $violations = [];

        foreach ($this->findEntityClasses() as $entityClass) {
            $hasRectifiableEntityAttribute = (new \ReflectionClass($entityClass))
                ->getAttributes(RectifiableEntity::class) !== [];

            if (! $hasRectifiableEntityAttribute) {
                continue;
            }

            if (! is_subclass_of($entityClass, AbstractEntity::class, true)) {
                $violations[] = sprintf(
                    '%s must extend %s.',
                    $entityClass,
                    AbstractEntity::class
                );
            }
        }

        return $violations;
    }

    /**
     * @return string[]
     */
    private function findEntityClasses(): array
    {
        $entityPath = $this->kernel->getProjectDir().'/src/Entity';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($entityPath)
        );
        $classes = [];

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace(
                $entityPath.DIRECTORY_SEPARATOR,
                '',
                $fileInfo->getPathname()
            );
            $className = 'App\\Entity\\'.str_replace(
                [DIRECTORY_SEPARATOR, '.php'],
                ['\\', ''],
                $relativePath
            );

            if (class_exists($className)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }
}
