<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    /**
     * Validate and get the entity class from entity name
     */
    protected function validateAndGetEntityClass(string $entityName, SymfonyStyle $io): ?string
    {
        $entityClass = $this->findEntityClass($entityName);
        if (!$entityClass) {
            $io->error(sprintf('Entity "%s" not found', $entityName));
            return null;
        }

        return $entityClass;
    }
}
