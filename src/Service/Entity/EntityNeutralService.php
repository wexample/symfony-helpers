<?php

namespace Wexample\SymfonyHelpers\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityNeutralService.
 * Helper to manage operations on entities.
 * Allowed to be used with no entity type.
 */
class EntityNeutralService
{
    public function __construct(
        public EntityManagerInterface $entityManager,
    ) {
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
