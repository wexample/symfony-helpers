<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Symfony\Component\Uid\Uuid;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;

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

    public static function getTableizedName(): string
    {
        return ClassHelper::getTableizedName(static::class);
    }

    public static function getCamelName(): string
    {
        return TextHelper::toCamel(ClassHelper::getShortName(static::class));
    }
}
