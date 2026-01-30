<?php

namespace Wexample\SymfonyHelpers\Helper;

use function array_map;
use function explode;
use function floatval;
use function floor;
use function implode;
use function is_string;
use function round;

class NumberHelper
{
    public static function numberToRoman(float $number): string
    {
        if (! static::isWholeNumber($number)) {
            return static::floatToRoman($number);
        }

        return static::intToRoman($number);
    }

    /**
     * Check if number is a whole number.
     *
     * This is the only found way to compare properly floats / double,
     * checking if it has a decimal. Same type comparison
     * with flooring number still returns false with some double values.
     */
    public static function isWholeNumber(float|int|string $number): bool
    {
        $number = is_string($number) ? floatval($number) : $number;

        return (string) $number == (int) floor($number);
    }

    public static function floatToRoman(float $number): string
    {
        return implode(
            '.',
            array_map(
                NumberHelper::class.'::numberToRoman',
                explode('.', (string) $number)
            )
        );
    }

    public static function intToRoman(int $number): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];
        $returnValue = '';

        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;

                    break;
                }
            }
        }

        return $returnValue;
    }

    /**
     * Round the int version (1524) of a float number (15.24), ex : 1500.
     */
    public static function roundIntData(int $int): int
    {
        return round(self::intDataToFloat($int)) * 100;
    }

    public static function toIntData(float $float): int
    {
        return round($float * 100);
    }

    public static function intDataToFloat(int $int): float
    {
        return $int / 100;
    }

    public static function isIntegerLike(mixed $value, bool $allowSign = true): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return false;
            }

            if ($allowSign && ($value[0] === '+' || $value[0] === '-')) {
                return $value !== '+' && $value !== '-' && ctype_digit(substr($value, 1));
            }

            return ctype_digit($value);
        }

        // Optional: accept numeric types that are whole numbers (e.g. 244.0)
        if (is_float($value)) {
            return is_finite($value) && floor($value) === $value;
        }

        return false;
    }
}
