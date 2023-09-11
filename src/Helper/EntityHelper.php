<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\SymfonyTranslations\Translation\Translator;
use function implode;

class EntityHelper
{
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
}