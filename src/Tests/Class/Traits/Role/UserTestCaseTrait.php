<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits\Role;

use Wexample\SymfonyHelpers\Helper\RoleHelper;

trait UserTestCaseTrait
{
    protected static function getRole(): string
    {
        return RoleHelper::ROLE_USER;
    }
}
