<?php

namespace Wexample\SymfonyHelpers\Helper;

use App\Wex\BaseBundle\Translation\Translator;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use function implode;

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
                \Wexample\SymfonyHelpers\Helper\ClassHelper::getTableizedName($entity),
            ]
        );
    }

    public static function getEntityPropertyTrans(
        $className,
        string $field,
        string $transGroup = 'property'
    ): string {
        return static::getEntityTransDomain($className).implode(
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
                \Wexample\SymfonyHelpers\Helper\ClassHelper::getTableizedName($className).Translator::DOMAIN_SEPARATOR,
            ]
        );
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
            $keyDateFieldValue = \Wexample\SymfonyHelpers\Helper\ClassHelper::getFieldGetterValue(
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
        array $entities,
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
        return \Wexample\SymfonyHelpers\Helper\ClassHelper::getRealClassPath($entity) === \Wexample\SymfonyHelpers\Helper\ClassHelper::getRealClassPath($entityB)
            && $entity->getId() === $entityB->getId();
    }
}