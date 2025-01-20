<?php

namespace Wexample\SymfonyHelpers\Helper;

class EnvironmentHelper
{
    final public const DEV = 'dev';

    final public const LOCAL = 'local';

    final public const PROD = 'prod';

    final public const STAGING = 'staging';

    final public const TEST = 'test';

    final public const LIST_UNREACHABLE = [
        self::LOCAL,
        self::TEST,
    ];

    final public const LIST_LOW_SECURITY = [
        self::DEV,
        self::LOCAL,
        self::TEST,
    ];

    public static function getMissingEnvKeys(array $expectedKeys): array
    {
        return array_filter($expectedKeys, function ($key) {
            return getenv($key) === false;
        });
    }
}
