<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;

trait EntityManipulationCommandTrait
{
    protected EntityManagerInterface $entityManager;

    protected function addEntityShortNameArgument(): static
    {
        return $this
            ->addArgument(
                'entity',
                InputArgument::REQUIRED,
                'Entity class name (short name without namespace)'
            );
    }

    /**
     * Find entity class by short name
     */
    protected function findEntityClass(string $entityName): ?string
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metadata as $classMetadata) {
            $shortName = $classMetadata->getReflectionClass()->getShortName();
            if ($shortName === $entityName) {
                return $classMetadata->getName();
            }
        }

        return null;
    }
}
