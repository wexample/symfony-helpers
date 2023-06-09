<?php

namespace Wexample\SymfonyHelpers\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Helper\TextHelper;

/**
 * @method QueryBuilder queryById(int $id, QueryBuilder $builder = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __call(
        $method,
        $arguments
    ) {
        if (str_starts_with($method, 'hasSome')) {
            return $this->findHasSome(
                // TODO ?
                $this->resolveMagicQueryCall(
                    $method,
                    $arguments
                )
            );
        }

        if (str_starts_with($method, 'queryBy')) {
            return $this->resolveMagicQueryCall($method, $arguments);
        }

        return parent::__call($method, $arguments);
    }

    protected function resolveMagicQueryCall(
        $method,
        $arguments
    ): QueryBuilder {
        $fieldName = lcfirst(substr($method, 7));

        if (!($this->_class->hasField($fieldName) || $this->_class->hasAssociation($fieldName))) {
            throw InvalidMagicMethodCall::becauseFieldNotFoundIn($this->_entityName, $fieldName, $method);
        }

        return $this->queryByField(
            $fieldName,
            $arguments[0],
            $arguments[1] ?? null
        );
    }

    public function queryByField(
        string $fieldName,
        $value,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->andWhere(
            $this->queryField($fieldName).' = :'.$fieldName.'Value'
        )
            ->setParameter($fieldName.'Value', $value);

        return $builder;
    }

    public function createOrGetQueryBuilder(
        QueryBuilder $builder = null
    ): ?QueryBuilder {
        // Search for interesting invoices.
        return $builder ?: $this->createQueryBuilder(
            $this->getEntityQueryAlias()
        );
    }

    public function getEntityQueryAlias(): string
    {
        $remove = 'app_entity_';

        $alias = self::createQueryAlias($this->getEntityName());

        // Remove useless first part if present.
        return (str_starts_with($alias, $remove)) ?
            substr(
                $alias,
                strlen($remove)
            ) : $alias;
    }

    public static function createQueryAlias(string $className): string
    {
        return str_replace(
            '\\',
            '_',
            TextHelper::toSnake($className)
        );
    }

    public function findHasSome(
        array $criteria,
        QueryBuilder $builder = null
    ): bool {
        $builder = $this->querySelectCount($builder);
        $builder->setMaxResults(1);

        foreach ($criteria as $fieldName => $value) {
            $this->queryByField(
                $fieldName,
                $value,
                $builder
            );
        }

        try {
            return (bool) $builder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Exception) {
            return false;
        }
    }

    public function querySelectCount(
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->select(
            'COUNT('.$this->queryField(AbstractEntity::PROPERTY_NAME_ID).')'
        );

        return $builder;
    }

    public function queryField(string $fieldName): string
    {
        return $this->getEntityQueryAlias().'.'.$fieldName;
    }

    public function add(AbstractEntity $entity, bool $flush = true): void
    {
        if (!class_parents($entity, $this->getEntityName())) {
            throw new \Exception('Entity of type ' . $entity::class . ' should be of type ' . $this->getEntityName() . ' in add() method');
        }

        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
