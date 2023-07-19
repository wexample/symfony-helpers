<?php

namespace Wexample\SymfonyHelpers\Helper;

class ArrayHelper
{
    public static function findGreatestArrayKey(array $array)
    {
        return \max(\array_keys($array));
    }

    public static function containsSameValues(
        array $array,
        $value = null
    ): bool {
        $value = \is_null($value) ? \current($array) : $value;

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
            $keys = \array_keys(\current($data));

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
        $max = \is_null($max) ? count($array) : $max;
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
}
