<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class EntityExtendRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        if ($entityReflection->getParentClass() === false) {
            $this->addAbstractEntityExtends($entityReflection);

            return [];
        }

        if (! is_subclass_of($entityReflection->getName(), AbstractEntity::class, true)) {
            return [
                sprintf(
                    '%s must extend %s.',
                    $entityReflection->getName(),
                    AbstractEntity::class
                ),
            ];
        }

        return [];
    }

    private function addAbstractEntityExtends(
        ReflectionClass $entityReflection
    ): void {
        $filePath = $entityReflection->getFileName();
        $content = file_get_contents($filePath);

        if (! str_contains($content, 'use Wexample\\SymfonyHelpers\\Entity\\AbstractEntity;')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse Wexample\\SymfonyHelpers\\Entity\\AbstractEntity;\n",
                $content,
                1
            );
        }

        $className = $entityReflection->getShortName();
        $content = preg_replace(
            '/\bclass\s+'.preg_quote($className, '/').'\b(?!\s+extends)(\s*(?:implements\s+[^{]+)?)\s*\{/m',
            'class '.$className.' extends AbstractEntity$1 {',
            $content,
            1
        );

        file_put_contents($filePath, $content);
    }
}
