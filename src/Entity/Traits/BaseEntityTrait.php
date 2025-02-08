<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Wexample\Helpers\Helper\ClassHelper;

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

    public function getTableizedName(): string
    {
        return ClassHelper::getTableizedName($this);
    }
}
