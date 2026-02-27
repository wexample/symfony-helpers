<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use Doctrine\ORM\Mapping\Entity;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class RepositoryCreateNewMethodRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $repositoryClass = $this->resolveRepositoryClass($entityReflection);
        if (! class_exists($repositoryClass)) {
            return [];
        }

        $repositoryReflection = new ReflectionClass($repositoryClass);
        $methodName = 'createNew'.$entityReflection->getShortName();
        $createMethodSignature = '';

        if ($repositoryReflection->hasMethod($methodName)) {
            $createMethodSignature = $this->buildMethodArgumentsSignature(
                $repositoryReflection->getMethod($methodName)
            );
        } else {
            $this->addCreateNewMethod(
                $repositoryReflection,
                $entityReflection
            );
        }

        $this->ensureSetNewAnnotation(
            $repositoryReflection,
            $entityReflection->getShortName(),
            $createMethodSignature
        );

        return [];
    }

    private function addCreateNewMethod(
        ReflectionClass $repositoryReflection,
        ReflectionClass $entityReflection
    ): void {
        $repositoryPath = $repositoryReflection->getFileName();
        $content = file_get_contents($repositoryPath);
        $entityShortName = $entityReflection->getShortName();
        $entityClass = $entityReflection->getName();

        if (! str_contains($content, 'use '.$entityClass.';')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse ".$entityClass.";\n",
                $content,
                1
            );
        }

        $methodName = 'createNew'.$entityShortName;
        $methodContent = <<<PHP

    public function {$methodName}(): {$entityShortName}
    {
        \$entity = new {$entityShortName}();
        \$entity->setGeneratedSecureId();

        return \$entity;
    }
PHP;

        $content = preg_replace('/\n}\s*$/', $methodContent."\n}", $content, 1);

        file_put_contents($repositoryPath, $content);
    }

    private function ensureSetNewAnnotation(
        ReflectionClass $repositoryReflection,
        string $entityShortName,
        string $methodArguments
    ): void {
        $repositoryPath = $repositoryReflection->getFileName();
        $content = file_get_contents($repositoryPath);
        $annotationLine = ' * @method '.$entityShortName.' setNew'.$entityShortName.'('.$methodArguments.')';

        if (str_contains($content, $annotationLine)) {
            return;
        }

        $docblockPattern = '/\/\*\*[\s\S]*?\*\//m';
        if (preg_match($docblockPattern, $content, $docblockMatch, PREG_OFFSET_CAPTURE)) {
            $docblock = $docblockMatch[0][0];
            $updatedDocblock = preg_replace('/\*\/\s*$/', " * @method ".$entityShortName." setNew".$entityShortName."(".$methodArguments.")\n */", $docblock, 1);
            $content = substr_replace(
                $content,
                $updatedDocblock,
                $docblockMatch[0][1],
                strlen($docblock)
            );
        } else {
            $repositoryShortName = $repositoryReflection->getShortName();
            $content = preg_replace(
                '/(^class\s+'.preg_quote($repositoryShortName, '/').'\b)/m',
                "/**\n".$annotationLine."\n */\n$1",
                $content,
                1
            );
        }

        file_put_contents($repositoryPath, $content);
    }

    private function buildMethodArgumentsSignature(
        ReflectionMethod $method
    ): string {
        $parts = [];

        foreach ($method->getParameters() as $parameter) {
            $parts[] = $this->buildParameterSignature($parameter);
        }

        return implode(', ', $parts);
    }

    private function buildParameterSignature(
        ReflectionParameter $parameter
    ): string {
        $type = $parameter->getType();
        $typeString = '';

        if ($type instanceof ReflectionNamedType) {
            $nullablePrefix = $type->allowsNull() && $type->getName() !== 'mixed' ? '?' : '';
            $typeString = $nullablePrefix.$type->getName().' ';
        } elseif ($type instanceof ReflectionUnionType) {
            $typeNames = [];
            foreach ($type->getTypes() as $unionType) {
                $typeNames[] = $unionType->getName();
            }
            $typeString = implode('|', $typeNames).' ';
        }

        if ($parameter->isPassedByReference()) {
            $typeString .= '&';
        }
        if ($parameter->isVariadic()) {
            $typeString .= '...';
        }

        $result = $typeString.'$'.$parameter->getName();
        if ($parameter->isDefaultValueAvailable() && ! $parameter->isVariadic()) {
            $result .= ' = '.var_export($parameter->getDefaultValue(), true);
        }

        return $result;
    }

    private function resolveRepositoryClass(
        ReflectionClass $entityReflection
    ): string {
        $entityAttributes = $entityReflection->getAttributes(
            Entity::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        if ($entityAttributes !== []) {
            /** @var Entity $entityAttribute */
            $entityAttribute = $entityAttributes[0]->newInstance();
            if ($entityAttribute->repositoryClass) {
                return $entityAttribute->repositoryClass;
            }
        }

        return sprintf(
            'App\\Repository\\%sRepository',
            $entityReflection->getShortName()
        );
    }
}
