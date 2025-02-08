<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\Helpers\Helper\TextHelper;

/**
 * Basic roles names, application should use its own helper
 * for custom roles in a class like ApplicationRoleHelper.
 */
class RoleHelper
{
    /**
     * string.
     */
    public const ROLE_ANONYMOUS = 'ROLE_ANONYMOUS';

    /**
     * string.
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * string.
     */
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * string.
     */
    public const ROLE_USER = 'ROLE_USER';

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_SUPER_ADMIN,
            self::ROLE_USER,
        ];
    }

    public static function flattenRolesConfig(array $roles): array
    {
        $output = [];

        foreach ($roles as $role) {
            $output[] = current($role);
        }

        return $output;
    }

    public static function getRoleNamePartAsClass(string $role): string
    {
        return TextHelper::toClass(
            strtolower(
                static::getRoleNamePart($role)
            )
        );
    }

    public static function getRoleNamePart(string $role): string
    {
        $exp = explode('_', $role);
        array_shift($exp);

        return implode('_', $exp);
    }

    public static function toKebabRoleName(string $roleName): string
    {
        return TextHelper::stringToKebab(
            strtolower(
                static::getRoleNamePart($roleName)
            )
        );
    }
}
