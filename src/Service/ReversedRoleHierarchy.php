<?php

/**
 * Created by PhpStorm.
 * User: weeger
 * Date: 09/02/19
 * Time: 22:52.
 */

namespace Wexample\SymfonyHelpers\Service;

use Symfony\Component\Security\Core\Role\RoleHierarchy;

class ReversedRoleHierarchy extends RoleHierarchy
{
    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     */
    public function __construct(array $hierarchy)
    {
        // Reverse the role hierarchy.
        $reversed = [];
        foreach ($hierarchy as $main => $roles) {
            foreach ($roles as $role) {
                $reversed[$role][] = $main;
            }
        }

        // Use the original algorithm to build the role map.
        parent::__construct($reversed);
    }

    /**
     * Helper function to get an array of strings.
     *
     * @return array An array of string role names
     */
    public function getParentRoles(string $roleName): array
    {
        return $this->getParentsRoles([$roleName]);
    }

    /**
     * Helper function to get an array of strings.
     *
     * @param array $roleNames An array of string role names
     *
     * @return array An array of string role names
     */
    public function getParentsRoles(array $roleNames): array
    {
        return $this->getReachableRoleNames($roleNames);
    }
}
