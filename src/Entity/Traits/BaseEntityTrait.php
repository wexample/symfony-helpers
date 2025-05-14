<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;

/**
 * Class TraitEntityStatus.
 *
 * Adding status management on an entity
 */
trait BaseEntityTrait
{
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public static function getTableizedName(): string
    {
        return ClassHelper::getTableizedName(static::class);
    }

    public static function getCamelName(): string
    {
        return TextHelper::toCamel(ClassHelper::getShortName(static::class));
    }
}
