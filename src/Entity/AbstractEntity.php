<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use Wexample\SymfonyHelpers\Helper\EntityHierarchyHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

/**
 * Mapped as a superclass, so that the id it declares is read once, here, for a
 * whole hierarchy of entities: a subclass joined to its root would otherwise
 * read the property again from its own reflection and map the column twice.
 */
#[MappedSuperclass]
abstract class AbstractEntity implements AbstractEntityInterface
{
    use BaseEntityTrait;

    /**
     * string.
     */
    public const PROPERTY_NAME_ID = VariableHelper::ID;

    #[Id]
    #[Column(type: UuidType::NAME, unique: true)]
    protected Uuid $id;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public static function buildEntityPath(
        string $className
    ): string {
        return ClassHelper::longTableizedToPath(
            ClassHelper::longTableized($className)
        );
    }

    /**
     * The name a hierarchy of entities shares: its root's. A subclass is known
     * to the api, the topics and the browser under it.
     */
    public static function getEntityKeyName(): string
    {
        return ClassHelper::getTableizedName(EntityHierarchyHelper::getRootClass(static::class));
    }

    public function getEntityShortName(): string
    {
        return static::getEntityKeyName().'#'.$this->getId();
    }
}
