<?php

namespace Wexample\SymfonyHelpers\Helper;

use function array_splice;
use function count;
use function is_numeric;
use function preg_match_all;
use function preg_replace;
use function str_contains;
use function str_replace;
use function strlen;
use function strpos;
use function strtolower;
use function substr;

/**
 * Equivalent of JavaScript DurationsHelper.
 */
class DurationsHelper
{
    final public const DAY_DURATION = 60 * 60 * 7;

    final public const UNIT_AUTO = 'auto';

    final public const UNIT_DURATION = 'duration';

    final public const UNIT_QUANTITY = 'quantity';

    final public const UNITS = [
        DurationsHelper::UNIT_DURATION,
        DurationsHelper::UNIT_QUANTITY,
    ];

    public static function filterDuration(string $duration): string
    {
        $duration = strtolower($duration);

        // Remove all white spaces.
        $duration = preg_replace('/\s/', '', $duration);

        // Keep only supported chars (french and english).
        $duration = preg_replace('/[^0-9jdhm]*/', '', $duration);

        // Keep only one letter.
        $duration = preg_replace(['/([a-z])[a-z]*/'], '$1', $duration);

        // First char should be only number,
        // remove all others.
        $duration = preg_replace('/^[^0-9]*/', '', $duration);

        // Handle if no more chars.
        if (! $duration) {
            $duration = 0;
        }

        // Duration is a number with any unit.
        if (is_numeric($duration)) {
            // Consider hours.
            $duration .= 'h';
        }

        // We have a "h" but no m,
        if (str_contains($duration, 'h')
            && ! str_contains($duration, 'm')
            // There is some chars after the "h"
            && strpos($duration, 'h') + 1 !== strlen($duration)
            // the extra chars are numbers.
            && is_numeric(substr($duration, strpos($duration, 'h') + 1))) {
            // Add trailing "m"
            $duration .= 'm';
        }

        return $duration;
    }

    public static function toDayQuantity(string $duration): float
    {
        // Language specific.
        if (strpos($duration, 'j')) {
            // French : j for "jour(s)" => day.
            $duration = str_replace('j', 'd', $duration);
        }

        // Split numbers and letters groups.
        $results = [];
        preg_match_all('/(\d+|[a-zA-Z]+)/', $duration, $results);
        $split = $results[1];
        $seconds = 0;

        while (is_countable($split) ? count($split) : 0) {
            $piece = array_splice($split, 0, 2);
            if (isset($piece[1])) {
                $number = (int) $piece[0];
                $unit = $piece[1][0];

                $seconds += match ($unit) {
                    'd' => $number * self::DAY_DURATION,
                    'h' => $number * 60 * 60,
                    'm' => $number * 60,
                };
            }
        }

        return $seconds / self::DAY_DURATION;
    }
}
