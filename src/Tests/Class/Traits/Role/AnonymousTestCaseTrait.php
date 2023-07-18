<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits\Role;

use Wexample\SymfonyHelpers\Helper\RoleHelper;

trait AnonymousTestCaseTrait
{
    protected static function getRole(): string
    {
        return RoleHelper::ROLE_ANONYMOUS;
    }
}
