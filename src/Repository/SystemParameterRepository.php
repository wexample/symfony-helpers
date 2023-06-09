<?php

namespace Wexample\SymfonyHelpers\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Wexample\SymfonyHelpers\Entity\SystemParameter;

/**
 * @method SystemParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemParameter|null findOneByName(string $name, array $orderBy = null)
 * @method SystemParameter[]    findAll()
 * @method SystemParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method int                  countByName(string $name)
 */
abstract class SystemParameterRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        $entityClass = SystemParameter::class
    ) {
        parent::__construct(
            $registry,
            $entityClass
        );
    }
}
