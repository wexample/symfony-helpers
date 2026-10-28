<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Symfony\Component\Uid\Uuid;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\EntityHierarchyHelper;

trait BaseEntityTrait
{
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    /**
     * Both names are the hierarchy's — the root's —: a subclass is one more
     * kind of the same entity, not another entity.
     */
    public static function getTableizedName(): string
    {
        return ClassHelper::getTableizedName(EntityHierarchyHelper::getRootClass(static::class));
    }

    public static function getCamelName(): string
    {
        return TextHelper::toCamel(ClassHelper::getShortName(EntityHierarchyHelper::getRootClass(static::class)));
    }
}
