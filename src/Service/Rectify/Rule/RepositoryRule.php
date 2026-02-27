<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use Doctrine\ORM\Mapping\Entity;
use ReflectionAttribute;
use ReflectionClass;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

class RepositoryRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $repositoryClass = $this->resolveRepositoryClass($entityReflection);

        if (! class_exists($repositoryClass)) {
            return [
                sprintf(
                    '%s must have repository %s.',
                    $entityReflection->getName(),
                    $repositoryClass
                ),
            ];
        }

        if (! is_subclass_of($repositoryClass, AbstractRepository::class, true)) {
            return [
                sprintf(
                    '%s repository %s must extend %s.',
                    $entityReflection->getName(),
                    $repositoryClass,
                    AbstractRepository::class
                ),
            ];
        }

        return [];
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
