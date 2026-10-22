# Declare symfony/security-core in composer.json

Opened: 2026-10-01
Updated: 2026-10-01
Author: agent:sapiens

## Context

Reported by the `symfony-forms` agent (commit `3c06379`, read-only fields for the Sapiens app), which had to carry `symfony/security-core` as `require-dev` for this package to load in its test kernel.

## Task

`src/Voter/AbstractVoter.php`, `src/Voter/AbstractLoggedUserEntityVoter.php` and `src/Service/ReversedRoleHierarchy.php` use `Symfony\Component\Security\Core`, but `composer.json` does not require it. It works only when the installing application brings it. Add it to `require` with the same constraint style as the other Symfony components (`^7.4 || ^8.0`).

Side note: `ReversedRoleHierarchy` here and `ReversedRoleHierarchyService` in `symfony-user` look like the same thing twice — worth checking whether this one is still used.

## Done

Added `"symfony/security-core": ">=6.2"` to `require`, matching the constraint the file already uses for every other Symfony component rather than the `^7.4 || ^8.0` suggested above.

`ReversedRoleHierarchy` is registered in `src/Resources/config/services.yaml` but no package under `PACKAGES/PHP` calls it. Left in place: whether `symfony-user`'s `ReversedRoleHierarchyService` replaces it is the owner's call.
