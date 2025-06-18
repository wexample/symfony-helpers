<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractEntity implements AbstractEntityInterface
{
    use BaseEntityTrait;

    /**
     * string.
     */
    public const PROPERTY_NAME_ID = VariableHelper::ID;

    #[Id]
    #[Column(type: Types::INTEGER)]
    #[GeneratedValue(strategy: "SEQUENCE")]
    protected $id;

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
