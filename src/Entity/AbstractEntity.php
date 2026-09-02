<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

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

    public static function getEntityKeyName(): string
    {
        return ClassHelper::getTableizedName(static::class);
    }

    public function getEntityShortName(): string
    {
        return ClassHelper::getTableizedName($this).'#'.$this->getId();
    }
}
