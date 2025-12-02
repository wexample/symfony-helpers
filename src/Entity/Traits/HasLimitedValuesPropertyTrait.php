<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use InvalidArgumentException;

trait HasLimitedValuesPropertyTrait
{
    /**
     * @throws InvalidArgumentException If the type is not allowed.
     */
    protected function checkAllowedOrFail(
        mixed $value,
        array|null $allowed
    ): void {
        // Check if allowed types are defined and if the type is in the allowed list
        if (is_array($allowed) && ! in_array($value, $allowed, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value "%s". Allowed types are: %s',
                $value,
                implode(', ', $allowed)
            ));
        }
    }
}
