<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;

class HasSecureIdTraitRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $entityPath = $entityReflection->getFileName();
        $content = file_get_contents($entityPath);
        $originalContent = $content;

        if (! str_contains($content, 'use Wexample\\SymfonyHelpers\\Entity\\Traits\\HasSecureIdTrait;')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse Wexample\\SymfonyHelpers\\Entity\\Traits\\HasSecureIdTrait;\n",
                $content,
                1
            );
        }

        if (! preg_match('/^\s*use\s+HasSecureIdTrait;\s*$/m', $content) && ! preg_match('/^\s*use\s+\\\\Wexample\\\\SymfonyHelpers\\\\Entity\\\\Traits\\\\HasSecureIdTrait;\s*$/m', $content)) {
            $entityShortName = $entityReflection->getShortName();
            $content = preg_replace(
                '/(class\s+'.preg_quote($entityShortName, '/').'\b[^{]*\{)\n/m',
                "$1\n    use HasSecureIdTrait;\n",
                $content,
                1
            );
        }

        if ($content !== $originalContent) {
            file_put_contents($entityPath, $content);
        }

        return [];
    }
}
