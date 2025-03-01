<?php

namespace Wexample\SymfonyHelpers\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class VectorType extends Type
{
    public const TYPE_NAME = 'vector';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $dimension = $column['options']['dimension'] ?? 1536;
        return "vector($dimension)";
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        return '[' . implode(',', array_map('floatval', $value)) . ']';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return [];
        }

        $value = trim($value, '[]');
        return array_map('floatval', explode(',', $value));
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
