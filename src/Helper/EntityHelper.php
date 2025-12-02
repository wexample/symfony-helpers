<?php

namespace Wexample\SymfonyHelpers\Helper;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;

use function implode;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use Wexample\SymfonyTranslations\Translation\Translator;

class EntityHelper
{
    final public const CLASS_BASE_PATH = 'App\\Entity\\';

    /**
     * @param BaseEntityTrait $entity
     */
    public static function createEntityId($entity): string
    {
        return implode(
            '-',
            [
                $entity->getId(),
                ClassHelper::getTableizedName($entity),
            ]
        );
    }

    public static function getEntityPropertyTrans(
        $className,
        string $field,
        string $transGroup = 'property'
    ): string {
        return static::getEntityTransDomain($className) . implode(
            '.',
            [
                    $transGroup,
                    TextHelper::toSnake($field),
                ]
        );
    }

    public static function getEntityTransDomain(
        $className
    ): string {
        return implode(
            '.',
            [
                'entity',
                ClassHelper::getTableizedName($className) . Translator::DOMAIN_SEPARATOR,
            ]
        );
    }

    /**
     * @param array<AbstractEntity>|Collection<int, AbstractEntity> $entities
     * @return array<AbstractEntity>|Collection<int, AbstractEntity>
     */
    public static function sortById(iterable $entities): iterable
    {
        $comparator = static fn (
            AbstractEntity $a,
            AbstractEntity $b
        ): int => $a->getId() <=> $b->getId();

        if ($entities instanceof Collection) {
            return $entities->matching(
                Criteria::create()->orderBy([VariableHelper::ID => Order::Ascending])
            );
        }

        $array = \is_array($entities) ? $entities : \iterator_to_array($entities, false);
        \usort($array, $comparator);

        return $array;
    }

    public static function sortMonthly(
        $entities,
        string $dateFieldName,
        DateTimeInterface $start,
        $end = null
    ): array {
        // First sort.
        $monthly = [];
        foreach ($entities as $entity) {
            $keyDateFieldValue = ClassHelper::getFieldGetterValue(
                $entity,
                $dateFieldName
            );
            $key = $keyDateFieldValue->format('Y-m');
            $monthly[$key][] = $entity;
        }

        // Second sort, fill empty slots if no data.
        // Get 1 month interval.
        $interval = new DateInterval('P1M');
        $end = $end ?: new DateTime();
        $period = new DatePeriod($start, $interval, $end);
        $monthlySorted = [];

        /** @var DateTime $date */
        foreach ($period as $date) {
            $key = $date->format(
                'Y-m'
            );
            $monthlySorted[$key] = $monthly[$key] ?? [];
        }

        return $monthlySorted;
    }

    public static function createRegistry(
        array|Collection $entities,
        array &$output = []
    ): array {
        /** @var BaseEntityTrait $entity */
        foreach ($entities as $entity) {
            $output[$entity->getId()] = $entity;
        }

        return $output;
    }

    public static function areSame(
        AbstractEntityInterface|string $entity,
        AbstractEntityInterface|string $entityB
    ): bool {
        return ClassHelper::getRealClassPath($entity) === ClassHelper::getRealClassPath($entityB)
            && $entity->getId() === $entityB->getId();
    }

    public static function getId(AbstractEntity|int|null $entity): ?int
    {
        return ClassHelper::isClassPath($entity, AbstractEntity::class) ? $entity->getId() : $entity;
    }

    public static function getEntityWithSmallestId(array $users): ?AbstractEntity
    {
        if ($users === []) {
            return null;
        }

        return array_reduce($users, function (
            ?AbstractEntity $carry,
            AbstractEntity $current
        ): AbstractEntity {
            if ($carry === null || $current->getId() < $carry->getId()) {
                return $current;
            }

            return $carry;
        });
    }
}
