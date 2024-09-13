<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Wexample\SymfonyHelpers\Helper\RoleHelper;

trait UserWithRolesTrait
{
    use HasRolesTrait {
        getRoles as baseGetRoles;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->baseGetRoles();
        // guarantee every user at least has ROLE_USER
        $roles[] = RoleHelper::ROLE_USER;

        return array_unique($roles);
    }
}
