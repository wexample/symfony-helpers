<?php

namespace Wexample\SymfonyHelpers\Helper;

use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use ReflectionClass;
use Wexample\Helpers\Helper\ClassHelper;

/**
 * What a hierarchy of entities is called, read off the mapping attributes alone.
 *
 * A hierarchy has one name, its root's: a `SyrtisSession` is a `session` to the
 * api, to the live topics, to the browser — the root is what they all know,
 * the subclass is what the row turns out to be. Nothing here asks Doctrine for
 * its metadata, so the same answer is given from a static method of an entity,
 * from a normalizer, from a test with no kernel: the attributes are the source
 * Doctrine reads too, and they do not move.
 */
class EntityHierarchyHelper
{
    /** What Doctrine names the column when the root names none. */
    final public const string DEFAULT_DISCRIMINATOR_NAME = 'dtype';

    /**
     * The topmost class of the hierarchy that is mapped as an entity: the class
     * itself when nothing above it is one. A mapped superclass or a plain
     * abstract class above the root does not count, as Doctrine does not count
     * it.
     */
    public static function getRootClass(object|string $entity): string
    {
        $class = ClassHelper::getRealClassPath($entity);
        $root = $class;

        for ($reflection = new ReflectionClass($class); $reflection = $reflection->getParentClass();) {
            if (! $reflection->getAttributes(Entity::class)) {
                break;
            }

            $root = $reflection->getName();
        }

        return $root;
    }

    /**
     * Whether the class takes part in an inheritance mapping: its root says so.
     */
    public static function isInHierarchy(object|string $entity): bool
    {
        return (bool) (new ReflectionClass(static::getRootClass($entity)))
            ->getAttributes(InheritanceType::class);
    }

    /**
     * The name of the discriminator column, which is the name the api gives
     * the property saying what a row turns out to be. The root names it
     * (`#[DiscriminatorColumn(name: 'engine')]`); Doctrine's own default
     * otherwise. Null outside a hierarchy.
     */
    public static function getDiscriminatorName(object|string $entity): ?string
    {
        if (! static::isInHierarchy($entity)) {
            return null;
        }

        $attributes = (new ReflectionClass(static::getRootClass($entity)))
            ->getAttributes(DiscriminatorColumn::class);

        return $attributes
            ? ($attributes[0]->newInstance()->name ?? self::DEFAULT_DISCRIMINATOR_NAME)
            : self::DEFAULT_DISCRIMINATOR_NAME;
    }

    /**
     * What a class is called within its hierarchy: its own name with the
     * root's taken off the end — `ClaudeSession` under `Session` is `claude` —,
     * the root being called after itself. The word the browser reads to tell
     * which subclass it holds, and the value the column stores. Null outside a
     * hierarchy.
     */
    public static function getDiscriminatorValue(object|string $entity): ?string
    {
        if (! static::isInHierarchy($entity)) {
            return null;
        }

        $class = ClassHelper::getRealClassPath($entity);
        $root = static::getRootClass($class);
        $name = ClassHelper::getTableizedName($class);

        if ($class === $root) {
            return $name;
        }

        $suffix = '_' . ClassHelper::getTableizedName($root);

        return str_ends_with($name, $suffix) && strlen($name) > strlen($suffix)
            ? substr($name, 0, -strlen($suffix))
            : $name;
    }

    /**
     * The discriminator map of a hierarchy, value to class, over the classes
     * given: what the Doctrine mapping is told to use in place of its default
     * so that the column holds the same word the api serves.
     *
     * @param iterable<class-string> $classes
     *
     * @return array<string, class-string>
     */
    public static function buildDiscriminatorMap(iterable $classes): array
    {
        $map = [];

        foreach ($classes as $class) {
            $map[static::getDiscriminatorValue($class)] = $class;
        }

        return $map;
    }
}
