<?php

namespace Wexample\SymfonyHelpers\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Attribute\RectifiableEntity;
use Wexample\SymfonyHelpers\Service\Rectify\Rule\AbstractRectifyRule;

class RectifyService
{
    public function __construct(
        private readonly KernelInterface $kernel,
        #[TaggedIterator('wexample.rectify.rule')]
        private readonly iterable $rules
    ) {
    }

    /**
     * @return string[]
     */
    public function validateRectifiableEntities(): array
    {
        $violations = [];

        foreach ($this->findEntityClasses() as $entityClass) {
            $reflectionClass = new ReflectionClass($entityClass);
            $hasRectifiableEntityAttribute = $reflectionClass
                ->getAttributes(RectifiableEntity::class) !== [];

            if (! $hasRectifiableEntityAttribute) {
                continue;
            }

            foreach ($this->rules as $rule) {
                /** @var AbstractRectifyRule $rule */
                foreach ($rule->apply($reflectionClass) as $violation) {
                    $violations[] = $violation;
                }
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
