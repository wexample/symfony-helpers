<?php

namespace Wexample\SymfonyHelpers\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

/**
 * @method AbstractEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractEntity[]    findAll()
 * @method AbstractEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method AbstractEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueryBuilder        queryById(int $id, QueryBuilder $builder = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    use EntityManipulatorTrait;

    public const QUERY_JOIN_TYPE_LEFT = 'left';
    public const QUERY_JOIN_TYPE_RIGHT = 'right';
    public const QUERY_JOIN_TYPE_DEFAULT = 'default';
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            static::getEntityClassName()
        );
    }

    public function __call(
        string $method,
        array $arguments
    ): mixed {
        if (str_starts_with($method, 'hasSome')) {
            return $this->findHasSome(
                $this->resolveMagicQueryCall(
                    $method,
                    $arguments
                )
            );
        }

        if (str_starts_with($method, 'queryBy')) {
            return $this->resolveMagicQueryCall($method, $arguments);
        }

        if (str_starts_with($method, 'createNew')) {
            return $this->resolveMagicCreateNewCall($method, $arguments);
        }

        if (str_starts_with($method, 'saveNew')) {
            return $this->resolveMagicSaveNewCall($method, $arguments);
        }

        if (str_starts_with($method, 'pluck')) {
            return $this->resolveMagicPluckCall($method, $arguments);
        }

        if (str_starts_with($method, 'countBy')) {
            return $this->resolveMagicCountByCall($method, $arguments);
        }

        if (str_starts_with($method, 'removeBy')) {
            return $this->resolveMagicRemoveCall($method, $arguments);
        }

        return parent::__call($method, $arguments);
    }

    /**
     * @throws InvalidMagicMethodCall
     */
    protected function resolveMagicQueryCall(
        $method,
        $arguments
    ): QueryBuilder {
        $fieldName = lcfirst(substr($method, 7));

        if (! ($this->getClassMetadata()->hasField($fieldName) || $this->getClassMetadata()->hasAssociation($fieldName))) {
            throw InvalidMagicMethodCall::becauseFieldNotFoundIn($this->_entityName, $fieldName, $method);
        }

        // Allow both named argument or second position argument as builder.
        $builder = null;
        if (isset($arguments['builder'])) {
            $builder = $arguments['builder'];
        } elseif (isset($arguments[1])) {
            $builder = $arguments[1];
        }
        $builder = $builder instanceof QueryBuilder ? $builder : null;

        return $this->queryByField(
            fieldName: $fieldName,
            value: $arguments[0],
            builder: $builder
        );
    }

    /**
     * @throws Exception
     */
    protected function resolveMagicCreateNewCall(
        $method,
        $arguments
    ): AbstractEntity {
        $entityShortName = TextHelper::removePrefix($method, 'saveNew');
        $expectedEntityShortName = ClassHelper::getShortName(static::getEntityClassName());

        if ($entityShortName !== $expectedEntityShortName) {
            throw new \Exception('Unable to create new entity of type "' . $entityShortName . '" from repository managing entities of type "' . $expectedEntityShortName . '".');
        }

        $createNewMethod = 'createNew' . $entityShortName;

        if (! method_exists($this, $createNewMethod)) {
            throw new \Exception('Creation method "' . $createNewMethod . '" not found on repository "' . static::class . '".');
        }

        return call_user_func_array(
            [
                $this,
                $createNewMethod,
            ],
            $arguments
        );
    }

    /**
     * @throws Exception
     */
    protected function resolveMagicSaveNewCall(
        $method,
        $arguments
    ): AbstractEntity {
        $entity = $this->resolveMagicCreateNewCall(
            $method,
            $arguments
        );

        $this->save($entity, flush: true);

        return $entity;
    }

    /**
     * @throws InvalidMagicMethodCall
     */
    protected function resolveMagicPluckCall(
        string $method,
        array $arguments
    ): array {
        $targetValueName = TextHelper::removePrefix($method, 'pluck');
        $getterMethod = 'get' . $targetValueName;
        $entities = $arguments[0] ?? [];

        if (! is_array($entities)) {
            throw new InvalidMagicMethodCall("Expected an array of entities for plucking.");
        }

        $output = [];

        foreach ($entities as $entity) {
            if (! method_exists($entity, $getterMethod)) {
                throw new InvalidMagicMethodCall("Method {$getterMethod} not found in " . get_class($entity));
            }
            $output[] = $entity->$getterMethod();
        }

        return $output;
    }

    /**
     * @throws InvalidMagicMethodCall
     */
    protected function resolveMagicCountByCall(
        string $method,
        array $arguments
    ): int {
        $fieldName = lcfirst(substr($method, 7));

        if (! ($this->getClassMetadata()->hasField($fieldName) || $this->getClassMetadata()->hasAssociation($fieldName))) {
            throw InvalidMagicMethodCall::becauseFieldNotFoundIn($this->_entityName, $fieldName, $method);
        }

        // Allow both named argument or second position argument as builder.
        $builder = null;
        if (isset($arguments['builder'])) {
            $builder = $arguments['builder'];
        } elseif (isset($arguments[1])) {
            $builder = $arguments[1];
        }
        $builder = $builder instanceof QueryBuilder ? $builder : null;

        $builder = $this->querySelectCount($builder);
        $this->queryByField(
            fieldName: $fieldName,
            value: $arguments[0],
            builder: $builder
        );

        try {
            return (int) $builder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception) {
            return 0;
        }
    }

    /**
     * @throws InvalidMagicMethodCall
     */
    protected function resolveMagicRemoveCall(
        string $method,
        array $arguments
    ): void {
        $fieldName = lcfirst(substr($method, 8));

        if (! ($this->getClassMetadata()->hasField($fieldName) || $this->getClassMetadata()->hasAssociation($fieldName))) {
            throw InvalidMagicMethodCall::becauseFieldNotFoundIn($this->_entityName, $fieldName, $method);
        }

        $entitiesToRemove = $this->findBy([$fieldName => $arguments[0]]);

        foreach ($entitiesToRemove as $entity) {
            $this->remove($entity, flush: false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Defines a default ordering criteria as some database might not have a consistent sorting method between same requests.
     * @param string $order
     * @param QueryBuilder|null $builder
     * @return QueryBuilder
     */
    public function orderByDefaultPagination(
        string $order = self::SORT_ASC,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder(
            $builder
        );

        $builder->orderBy(
            sort: $this->queryField(VariableHelper::ID),
            order: $order
        );

        return $builder;
    }

    public function queryPaginated(
        int $page,
        ?int $length = null,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);
        if ($length and $length > 0) {
            $builder->setMaxResults($length);
        }

        $builder = $this->orderByDefaultPagination(builder: $builder);

        $builder->setFirstResult($page * $length);

        return $builder;
    }

    public function findPaginated(
        int $page,
        ?int $length,
    ): array {
        return $this->queryPaginated($page, $length)
            ->getQuery()
            ->execute();
    }

    public function queryByField(
        string $fieldName,
        $value,
        ?string $entityName = null,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->andWhere(
            $this->queryField(
                fieldName: $fieldName,
                entityName: $entityName
            ) . ' = :' . $fieldName . 'Value'
        )
            ->setParameter($fieldName . 'Value', $value);

        return $builder;
    }

    public function createOrGetQueryBuilder(
        QueryBuilder $builder = null,
        ?string $aliasSuffix = null
    ): ?QueryBuilder {
        // Search for interesting invoices.
        return $builder ?: $this->createQueryBuilder(
            $this->getEntityQueryAlias(aliasSuffix: $aliasSuffix)
        );
    }

    public function getEntityQueryAlias(
        ?string $entityName = null,
        ?string $aliasSuffix = null
    ): string {
        $remove = 'app_entity_';

        $alias = self::createQueryAlias($entityName ?: $this->getEntityName());

        // Remove useless first part if present.
        return ((str_starts_with($alias, $remove)) ?
                substr(
                    $alias,
                    strlen($remove)
                ) : $alias) . ($aliasSuffix ? '_' . $aliasSuffix : '');
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
        } catch (Exception) {
            return false;
        }
    }

    public function querySelectCount(
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->select(
            'COUNT(' . $this->queryField(AbstractEntity::PROPERTY_NAME_ID) . ')'
        );

        return $builder;
    }

    public function removeAll(
        array|Collection $entities,
        bool $flush = true
    ): void {
        /** @var AbstractEntity $entry */
        foreach ($entities as $entry) {
            $this->remove($entry);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function queryField(
        string $fieldName,
        ?string $entityName = null,
        ?string $aliasSuffix = null
    ): string {
        return $this->getEntityQueryAlias(entityName: $entityName, aliasSuffix: $aliasSuffix) . '.' . $fieldName;
    }

    public function queryJoinEntity(
        string $targetEntityClassName,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $key = $targetEntityClassName::getEntityKeyName();
        $this->createOrGetQueryBuilder($builder)->join(
            $this->getEntityName()::getEntityKeyName() . '.' . $key,
            $key
        );

        return $builder;
    }

    public function queryRelatedToEntityHavingFieldValue(
        string $targetEntityClassName,
        string $fieldName,
        $value,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->queryJoinEntity(
            targetEntityClassName: $targetEntityClassName,
            builder: $builder
        );

        return $this->queryByField(
            fieldName: $fieldName,
            value: $value,
            entityName: $targetEntityClassName,
            builder: $builder
        );
    }

    /**
     * @throws Exception
     */
    public function add(
        AbstractEntity $entity,
        bool $flush = true
    ): AbstractEntity {
        if (! class_parents($entity, $this->getEntityName())) {
            throw new Exception('Entity of type ' . $entity::class . ' should be of type ' . $this->getEntityName() . ' in add() method');
        }

        $em = $this->getEntityManager();
        $em->persist($entity);
        if ($flush) {
            $em->flush();
        }

        return $entity;
    }

    public function save(
        AbstractEntity $entity,
        bool $flush = true
    ): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(
        AbstractEntity $entity,
        bool $flush = true
    ): bool {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return true;
    }

    public function findAllSorted(
        string $order = self::SORT_ASC
    ): array {
        return $this->orderByDefaultPagination($order)
            ->getQuery()
            ->getResult();
    }

    protected function findRelatedEntity(
        string $entityClass,
        int $entityId
    ): ?AbstractEntity {
        $repository = $this->getEntityManager()->getRepository($entityClass);
        if ($entity = $repository->find($entityId)) {
            return $entity;
        }

        return null;
    }

    public function getDefaultIdentifierName(): string
    {
        return 'id';
    }

    public function findOneByDefaultIdentifier(string|int $identifier): ?AbstractEntity
    {
        return $this->findOneBy([
            $this->getDefaultIdentifierName() => $identifier,
        ]);
    }
}
