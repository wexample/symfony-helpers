<?php

namespace Wexample\SymfonyHelpers\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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

        foreach ($this->findEntityClasses() as $entityClass) {
            $reflectionClass = new ReflectionClass($entityClass);
            $hasRectifiableEntityAttribute = $reflectionClass
                ->getAttributes(RectifiableEntity::class) !== [];

            if (! $hasRectifiableEntityAttribute) {
                continue;
            }

            $parentClass = $reflectionClass->getParentClass();

            if ($parentClass === false) {
                $this->addAbstractEntityExtends($reflectionClass);
                $this->removeDefaultIdBlockIfItIsTheOnlyClassContent($reflectionClass);

                continue;
            }

            if (! is_subclass_of($entityClass, AbstractEntity::class, true)) {
                $violations[] = sprintf(
                    '%s must extend %s.',
                    $entityClass,
                    AbstractEntity::class
                );
            }

            $this->removeDefaultIdBlockIfItIsTheOnlyClassContent($reflectionClass);
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

    private function addAbstractEntityExtends(
        ReflectionClass $reflectionClass
    ): void {
        $filePath = $reflectionClass->getFileName();
        $content = file_get_contents($filePath);

        if (! str_contains($content, 'use Wexample\\SymfonyHelpers\\Entity\\AbstractEntity;')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse Wexample\\SymfonyHelpers\\Entity\\AbstractEntity;\n",
                $content,
                1
            );
        }

        $className = $reflectionClass->getShortName();
        $content = preg_replace(
            '/\bclass\s+'.preg_quote($className, '/').'\b(?!\s+extends)(\s*(?:implements\s+[^{]+)?)\s*\{/m',
            'class '.$className.' extends AbstractEntity$1 {',
            $content,
            1
        );

        file_put_contents($filePath, $content);
    }

    private function removeDefaultIdBlockIfItIsTheOnlyClassContent(
        ReflectionClass $reflectionClass
    ): void {
        $filePath = $reflectionClass->getFileName();
        $content = file_get_contents($filePath);
        $className = $reflectionClass->getShortName();

        $pattern = '/(class\s+'.preg_quote($className, '/').'\b[^{]*\{)(?<body>[\s\S]*?)(^\})/m';
        if (! preg_match($pattern, $content, $matches)) {
            return;
        }

        $normalizedBody = preg_replace('/\s+/', '', $matches['body']);
        $normalizedSymfonyDefaultIdBlock = preg_replace('/\s+/', '', <<<'PHP'
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

public function getId(): ?int
{
    return $this->id;
}
PHP);

        if ($normalizedBody !== $normalizedSymfonyDefaultIdBlock) {
            return;
        }

        $content = preg_replace(
            $pattern,
            '$1'."\n".'$3',
            $content,
            1
        );

        file_put_contents($filePath, $content);
    }
}
