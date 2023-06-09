<?php

namespace Wexample\SymfonyHelpers\Repository;

use App\Entity\SystemParameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemParameter[]    findAll()
 * @method SystemParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
