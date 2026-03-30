<?php

namespace Wexample\SymfonyHelpers\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Wexample\SymfonyHelpers\Entity\AbstractUser;

trait AccessControlledRepositoryTrait
{
    public function queryPaginatedForUser(
        int $page,
        ?int $length,
        AbstractUser $user,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->queryAccessFilter($user, $builder);

        return $this->queryPaginated($page, $length, $builder);
    }

    public function findPaginatedForUser(
        int $page,
        ?int $length,
        $user
    ): array {
        return $this->queryPaginatedForUser($page, $length, $user)
            ->getQuery()
            ->execute();
    }

    abstract protected function queryAccessFilter(
        AbstractUser $user,
        QueryBuilder $builder = null
    ): QueryBuilder;
}
