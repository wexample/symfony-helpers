<?php

namespace Wexample\SymfonyHelpers\Helper;

use function round;

use Wexample\Helpers\Helper\TextHelper;

class PriceHelper
{
    final public const CURRENCY_BIF = 'BIF';

    final public const CURRENCY_CHF = 'CHF';

    final public const CURRENCY_CLP = 'CLP';

    final public const CURRENCY_DJF = 'DJF';

    final public const CURRENCY_EURO = 'EUR';

    final public const CURRENCY_GNF = 'GNF';

    final public const CURRENCY_JPY = 'JPY';

    final public const CURRENCY_KMF = 'KMF';

    final public const CURRENCY_KRW = 'KRW';

    final public const CURRENCY_MGA = 'MGA';

    final public const CURRENCY_PYG = 'PYG';

    final public const CURRENCY_RWF = 'RWF';

    final public const CURRENCY_UGX = 'UGX';

    final public const CURRENCY_VND = 'VND';

    final public const CURRENCY_VUV = 'VUV';

    final public const CURRENCY_XAF = 'XAF';

    final public const CURRENCY_XOF = 'XOF';

    final public const CURRENCY_XPF = 'XPF';

    final public const ZERO_DECIMAL_CURRENCIES = [
        self::CURRENCY_BIF,
        self::CURRENCY_CLP,
        self::CURRENCY_DJF,
        self::CURRENCY_GNF,
        self::CURRENCY_JPY,
        self::CURRENCY_KMF,
        self::CURRENCY_KRW,
        self::CURRENCY_MGA,
        self::CURRENCY_PYG,
        self::CURRENCY_RWF,
        self::CURRENCY_UGX,
        self::CURRENCY_VND,
        self::CURRENCY_VUV,
        self::CURRENCY_XAF,
        self::CURRENCY_XOF,
        self::CURRENCY_XPF,
    ];

    public static function isZeroDecimalCurrency(
        string $currency
    ): bool {
        return in_array(
            $currency,
            self::ZERO_DECIMAL_CURRENCIES
        );
    }

    /**
     * Based on Stripe currency management,
     * uses always the minimal currency unit,
     * so EUR should be converted in cents, but JPY should not,
     * as there is no cents in this currency.
     */
    public static function buildPriceFromFloat(
        float $price,
        string $currency
    ): int {
        if (self::isZeroDecimalCurrency($currency)) {
            return (int) $price;
        } else {
            return (int) round($price * 100);
        }
    }

    public static function addInitialPercentage(
        int $number,
        int $percentage
    ): int {
        return static::subtractInitialPercentage(
            $number,
            -$percentage
        );
    }

    /**
     * Subtract a given percentage to find the original value.
     * ie : Find the price before the tax have been applied.
     *
     * @param int $number     the price int data
     * @param int $percentage The int data of the stored value : 100 ==> 1%
     */
    public static function subtractInitialPercentage(
        int $number,
        int $percentage
    ): int {
        return round($number / ((10000 + $percentage) / 10000));
    }

    public static function calcPercentageAmountOnInitialValue(
        int $number,
        int $percentage
    ): int {
        return round(
            $number - static::subtractInitialPercentage($number, $percentage)
        );
    }

    public static function subtractPercentage(
        int $number,
        int $percentage
    ): int {
        return round(
            $number - static::calcPercentage($number, $percentage)
        );
    }

    /**
     * Given arguments : 20000 / 300 = 600
     * Theory          : 200,00 / 3% = 6
     */
    public static function calcPercentage(
        int $number,
        int $percentage
    ): int {
        return round(
            NumberHelper::intDataToFloat($number) / 100 * $percentage
        );
    }

    public static function addPercentage(
        int $number,
        int $percentage
    ): int {
        return round($number + static::calcPercentage($number, $percentage));
    }

    public static function priceOrNull(
        int|null $price,
        $currency = '€'
    ): ?string {
        return ! is_null($price) ? self::price($price, $currency) : $price;
    }

    public static function price(
        int $price,
        $currency = '€'
    ): string {
        // French format.
        return TextHelper::getStringFromIntData($price).($currency ? ' '.$currency : '');
    }

    public static function priceToInt(string|int|float|null $price): int
    {
        return
            NumberHelper::toIntData(
                TextHelper::getFloatFromString($price ?? 0)
            );
    }
}
