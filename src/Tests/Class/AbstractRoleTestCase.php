<?php

namespace Wexample\SymfonyHelpers\Tests\Class;

use Wexample\SymfonyHelpers\Tests\Class\AbstractSymfonyTestCase;

abstract class AbstractRoleTestCase extends AbstractSymfonyTestCase
{
    public static function getRoleTestClassBasePath(): string
    {
        return '\\App\\Tests\\Application\\Role\\';
    }

    abstract protected static function getRole(): string;
}
