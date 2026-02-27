<?php

namespace Wexample\SymfonyHelpers\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
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
        $entityClasses = $this->findEntityClasses();

        foreach ($entityClasses as $entityClass) {
            $reflectionClass = new ReflectionClass($entityClass);
            $attributes = $reflectionClass->getAttributes(RectifiableEntity::class, ReflectionAttribute::IS_INSTANCEOF);

            if ($attributes === []) {
                continue;
            }

            /** @var RectifiableEntity $attribute */
            $attribute = $attributes[0]->newInstance();
            $entityViolations = $this->validateEntity(
                $reflectionClass,
                $attribute->config
            );

            foreach ($entityViolations as $entityViolation) {
                $violations[] = $entityViolation;
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

    /**
     * @return string[]
     */
    private function validateEntity(
        ReflectionClass $entityReflection,
        array $config
    ): array {
        $violations = [];
        $allowedKeys = ['extends', 'traits', 'properties', 'classes'];
        $unknownKeys = array_diff(array_keys($config), $allowedKeys);
        if ($unknownKeys !== []) {
            throw new \RuntimeException(sprintf(
                'Unsupported RectifiableEntity config keys on %s: %s',
                $entityReflection->getName(),
                implode(', ', $unknownKeys)
            ));
        }

        $expectedParentClasses = array_unique([
            AbstractEntity::class,
            ...$this->normalizeStringList($config, 'extends'),
        ]);

        foreach ($expectedParentClasses as $expectedParentClass) {
            if (! is_subclass_of($entityReflection->getName(), $expectedParentClass, true)) {
                $violations[] = sprintf(
                    '%s must extend %s.',
                    $entityReflection->getName(),
                    $expectedParentClass
                );
            }
        }

        $entityTraits = $this->collectTraits($entityReflection);
        foreach ($this->normalizeStringList($config, 'traits') as $expectedTrait) {
            if (! in_array($expectedTrait, $entityTraits, true)) {
                $violations[] = sprintf(
                    '%s must use trait %s.',
                    $entityReflection->getName(),
                    $expectedTrait
                );
            }
        }

        foreach ($this->normalizeStringList($config, 'properties') as $expectedProperty) {
            if (! $entityReflection->hasProperty($expectedProperty)) {
                $violations[] = sprintf(
                    '%s must declare property $%s.',
                    $entityReflection->getName(),
                    $expectedProperty
                );
            }
        }

        foreach ($this->normalizeStringList($config, 'classes') as $classPattern) {
            $expectedClass = $this->resolveClassPattern(
                $classPattern,
                $entityReflection
            );

            if (! class_exists($expectedClass)) {
                $violations[] = sprintf(
                    '%s expects class %s to exist.',
                    $entityReflection->getName(),
                    $expectedClass
                );
            }
        }

        return $violations;
    }

    /**
     * @return string[]
     */
    private function normalizeStringList(
        array $config,
        string $key
    ): array {
        if (! array_key_exists($key, $config)) {
            return [];
        }

        $value = $config[$key];
        if (is_string($value)) {
            return [$value];
        }

        if (! is_array($value) || ! array_is_list($value)) {
            throw new \RuntimeException(sprintf(
                'RectifiableEntity config key "%s" must be a string or a list of strings.',
                $key
            ));
        }

        foreach ($value as $item) {
            if (! is_string($item)) {
                throw new \RuntimeException(sprintf(
                    'RectifiableEntity config key "%s" must contain only strings.',
                    $key
                ));
            }
        }

        return $value;
    }

    /**
     * @return string[]
     */
    private function collectTraits(
        ReflectionClass $entityReflection
    ): array {
        $traitNames = [];
        $currentClass = $entityReflection;

        while ($currentClass !== false) {
            foreach ($currentClass->getTraitNames() as $traitName) {
                $traitNames[$traitName] = $traitName;
                foreach ($this->collectTraitDependencies($traitName) as $dependencyTraitName) {
                    $traitNames[$dependencyTraitName] = $dependencyTraitName;
                }
            }

            $currentClass = $currentClass->getParentClass();
        }

        return array_values($traitNames);
    }

    /**
     * @return string[]
     */
    private function collectTraitDependencies(
        string $traitName
    ): array {
        $traitReflection = new ReflectionClass($traitName);
        $traitNames = [];

        foreach ($traitReflection->getTraitNames() as $dependencyTraitName) {
            $traitNames[$dependencyTraitName] = $dependencyTraitName;
            foreach ($this->collectTraitDependencies($dependencyTraitName) as $subTraitName) {
                $traitNames[$subTraitName] = $subTraitName;
            }
        }

        return array_values($traitNames);
    }

    private function resolveClassPattern(
        string $pattern,
        ReflectionClass $entityReflection
    ): string {
        $entityNamespace = $entityReflection->getNamespaceName();
        $baseNamespace = preg_replace('/\\\\Entity(?:\\\\.*)?$/', '', $entityNamespace) ?: $entityNamespace;

        return strtr($pattern, [
            '%entity%' => $entityReflection->getShortName(),
            '%entity_short%' => $entityReflection->getShortName(),
            '%entity_fqcn%' => $entityReflection->getName(),
            '%entity_namespace%' => $entityNamespace,
            '%base_namespace%' => $baseNamespace,
        ]);
    }
}
