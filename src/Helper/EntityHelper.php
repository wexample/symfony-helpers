<?php

namespace Wexample\SymfonyHelpers\Helper;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\Proxy;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use Wexample\SymfonyTranslations\Translation\Translator;
use function implode;
use function str_contains;
use function str_replace;

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
                ClassHelper::getTableizedName($className).Translator::DOMAIN_SEPARATOR,
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
        return ClassHelper::getRealClassPath($entity) === ClassHelper::getRealClassPath($entityB)
            && $entity->getId() === $entityB->getId();
    }

    public static function getId(AbstractEntity|int|null $entity): ?int
    {
        return ClassHelper::isClassPath($entity, Product::class) ? $entity->getId() : $entity;
    }

    /**
     * Normalize a class name by removing Doctrine proxy prefixes
     * This handles both standard Doctrine proxies and Symfony's specific proxy format
     *
     * @param string $className The class name to normalize
     * @return string The normalized class name
     */
    public static function getRealClassName(object|string $className): string
    {
        $className = is_object($className) ? $className::class : $className;

        // Check if it's a Doctrine proxy class with Symfony's specific format
        if (str_contains($className, 'Proxies\\__CG__\\')) {
            // Extract the real entity class name from the proxy class name
            return str_replace('Proxies\\__CG__\\', '', $className);
        }
        
        // Use ClassHelper's method for other proxy types
        if (class_exists($className) && is_subclass_of($className, Proxy::class)) {
            try {
                return ClassHelper::getRealClassPath($className);
            } catch (\ReflectionException $e) {
                // Fallback to the original class name if reflection fails
                return $className;
            }
        }
        
        return $className;
    }
}
