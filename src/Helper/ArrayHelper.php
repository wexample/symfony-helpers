<?php

namespace Wexample\SymfonyHelpers\Helper;

use function array_keys;
use function current;
use function is_null;
use function max;

class ArrayHelper
{
    public static function findGreatestArrayKey(array $array)
    {
        return max(array_keys($array));
    }

    public static function pickKeys(
        array $array,
        array $keys
    ): array {
        return array_intersect_key(
            $array,
            array_flip($keys)
        );
    }

    public static function containsSameValues(
        array $array,
        $value = null
    ): bool {
        $value = is_null($value) ? current($array) : $value;

        foreach ($array as $item) {
            if ($item !== $value) {
                return false;
            }
        }

        return true;
    }

    public static function toHtmlTable(array $data): string
    {
        $output = '';

        if (!empty($data)) {
            $keys = array_keys(current($data));

            $output .= '<thead><tr>';

            foreach ($keys as $key) {
                $output .= '<th>'.$key.'</th>';
            }

            $output .= '</tr></thead><tbody>';

            foreach ($data as $line) {
                $output .= '<tr>';

                // Based on the keys names from the first cell.
                foreach ($keys as $key) {
                    $output .= '<td>'.($line[$key] ?? '').'</td>';
                }

                $output .= '</tr>';
            }

            $output .= '</tbody>';
        }

        return '<table>'.$output.'</table>';
    }

    public static function removeItem(
        array $array,
        $itemSearch,
        bool $multiple = true
    ): array {
        $output = [];
        $found = false;

        foreach ($array as $key => $item) {
            if ($item === $itemSearch && (!$found || $multiple)) {
                $found = true;
            } else {
                $output[$key] = $item;
            }
        }

        return $output;
    }

    public static function removeKeys(
        array $array,
        array $keys
    ): array {
        return array_diff_key(
            $array,
            array_flip(
                $keys
            )
        );
    }

    public static function wrapIfNotArray(mixed $value): array
    {
        return is_array($value) ? $value : [$value];
    }

    public static function countNonArrayValues(array $array): int
    {
        $total = 0;

        foreach ($array as $item) {
            if (is_array($item)) {
                $total += static::countNonArrayValues($item);
            } else {
                ++$total;
            }
        }

        return $total;
    }

    public static function generateAllIncompleteCombinations(array $array): array
    {
        $max = count($array);

        // Keep only combinations under array total size.
        return ArrayHelper::truncateChildrenArrayByLength(
        // Bigger length first.
            ArrayHelper::sortOnChildrenArrayLength(
            // Every possible fields combinations.
                ArrayHelper::generateAllCombinations($array)
            ),
            0,
            $max - 1
        );
    }

    public static function truncateChildrenArrayByLength(
        array $array,
        ?int $min = 0,
        int $max = null
    ): array {
        $max = is_null($max) ? count($array) : $max;
        $output = [];

        foreach ($array as $key => $child) {
            $count = count($child);
            if ($count >= $min && $count <= $max) {
                $output[$key] = $child;
            }
        }

        return $output;
    }

    public static function sortOnChildrenArrayLength(array $array): array
    {
        uasort($array, function(
            $a,
            $b
        ) {
            return count($b) - count($a);
        });

        return $array;
    }

    public static function generateAllCombinations(array $array): array
    {
        // Initialize by adding the empty set.
        $results = [[]];

        foreach ($array as $key => $element) {
            foreach ($results as $combination) {
                $results[] = array_merge([$key => $element], $combination);
            }
        }

        return $results;
    }

    public static function filterOnInt(
        array $array,
        string|int $key,
        int $search,
    ): array {
        return array_values(
            array_filter($array, fn(
                $result
            ) => isset($result[$key])
                && is_numeric($result[$key])
                && (int) $result[$key] === $search
            )
        );
    }

    public static function sortOn(
        array $array,
        string|int $key
    ): array {
        usort($array, function(
            $a,
            $b
        ) use
        (
            $key
        ) {
            return $a[$key] <=> $b[$key];
        });

        return $array;
    }

    public static function toStringTable(array $array): array
    {
        $table = [];

        // First pass, transform objects into arrays
        foreach ($array as $key => $item)  {
            if (is_object($item)) {
                $array[$key] = (array) $item;
            }
        }

        // Items keys should be consistent
        if (!self::hasTableStructure($array)) {
            return $table;
        }

        foreach ($array as $line) {
            $lineConverted = [];

            foreach ($line as $key => $value) {
                $lineConverted[$key] = TextHelper::toString($value);
            }

            $table[] = $lineConverted;
        }

        return $table;
    }

    /**
     * Return true if array can be rendered as a two-dimensional table (no su table, same columns names).
     */
    public static function hasTableStructure(
        array $array,
        bool $acceptMissing = false
    ): bool {
        if (empty($array)) {
            return true;
        }

        // First item should be an array to allow getting base keys.
        $first = current($array);
        if (!is_array($first)) {
            return false;
        }

        $baseKeys = array_keys($first);
        foreach ($array as $item) {
            if (!is_array($item)) {
                return false;
            }

            $itemKeys = array_keys($item);

            if (!$acceptMissing && count($baseKeys) !== count($itemKeys)) {
                return false;
            }

            if (!empty(array_diff($baseKeys, $itemKeys))) {
                return false;
            }
        }

        return true;
    }
}
