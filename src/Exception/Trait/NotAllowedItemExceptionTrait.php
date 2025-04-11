<?php

namespace Wexample\SymfonyHelpers\Exception\Trait;

/**
 * Trait for exceptions related to items that are not allowed in a list of allowed items.
 *
 * This trait provides utility methods to format error messages for cases where
 * a provided item is not found in a list of allowed items.
 */
trait NotAllowedItemExceptionTrait
{
    /**
     * Formats an error message for an item that is not allowed.
     *
     * @param string $itemType The type of item (e.g., 'format', 'option', 'value')
     * @return string The formatted error message
     */
    protected function formatNotAllowedItemMessage(
        string $itemType,
        string $itemValue,
        array $allowedValues
    ): string
    {
        return sprintf(
            "The %s '%s' is not allowed. Allowed values are: '%s'.",
            $itemType,
            $itemValue,
            implode(
                "', '",
                $allowedValues
            )
        );
    }
}
