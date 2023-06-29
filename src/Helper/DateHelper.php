<?php

namespace Wexample\SymfonyHelpers\Helper;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use IntlDateFormatter;

class DateHelper
{
    final public const DATE_PATTERN_PART_YEAR_FULL = 'Y';
    final public const DATE_PATTERN_PART_MONTH_FULL = 'm';
    final public const DATE_PATTERN_PART_DAY_FULL = 'd';
    final public const DATE_PATTERN_PART_HOURS_FULL = 'H';
    final public const DATE_PATTERN_PART_MINUTES_FULL = 'i';
    final public const DATE_PATTERN_PART_SECONDS_FULL = 's';
    final public const DATE_PATTERN_DAY_DEFAULT =
        self::DATE_PATTERN_PART_YEAR_FULL.'-'.
        self::DATE_PATTERN_PART_MONTH_FULL.'-'.
        self::DATE_PATTERN_PART_DAY_FULL;
    final public const DATE_PATTERN_DAY_REVERTED =
        self::DATE_PATTERN_PART_YEAR_FULL.'-'.
        self::DATE_PATTERN_PART_DAY_FULL.'-'.
        self::DATE_PATTERN_PART_MONTH_FULL;
    final public const DATE_PATTERN_YMD_FR =
        self::DATE_PATTERN_PART_DAY_FULL.'/'.
        self::DATE_PATTERN_PART_MONTH_FULL.'/'.
        self::DATE_PATTERN_PART_YEAR_FULL;
    final public const TIME_PATTERN_SECOND_DEFAULT = self::DATE_PATTERN_PART_HOURS_FULL.':'.self::DATE_PATTERN_PART_MINUTES_FULL.':'.self::DATE_PATTERN_PART_SECONDS_FULL;
    final public const DATE_PATTERN_TIME_DEFAULT = self::DATE_PATTERN_DAY_DEFAULT.' '.self::TIME_PATTERN_SECOND_DEFAULT;
    final public const DATE_PATTERN_TIME_REVERTED = self::DATE_PATTERN_DAY_REVERTED.' '.self::TIME_PATTERN_SECOND_DEFAULT;
    final public const DATE_PATTERN_ISO08601 = self::DATE_PATTERN_DAY_DEFAULT.'\T'.self::TIME_PATTERN_SECOND_DEFAULT;
    // @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/
    final public const INTL_DATE_FORMATTER_MONTH_FULL = 'MMMM';
    final public const INTL_DATE_FORMATTER_YEAR_FULL = 'YYYY';
    final public const INTL_DATE_PATTERN_MONTH_AND_YEAR_FULL =
        self::INTL_DATE_FORMATTER_MONTH_FULL
        .' '.self::INTL_DATE_FORMATTER_YEAR_FULL;
    final public const QUERY_STRING_DATE_FORMATS = [
        self::DATE_PATTERN_TIME_DEFAULT,
        'Y-m-d H:i',
        'Y-m-d H',
        'Y-m-d',
        'Y-m',
        self::DATE_PATTERN_PART_YEAR_FULL,
    ];

    public static function generateFromTimestamp(int $timestamp): DateTimeInterface
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    public static function buildFromYear(int $year): DateTimeInterface
    {
        return DateTime::createFromFormat(
            self::DATE_PATTERN_PART_YEAR_FULL,
            $year
        );
    }

    public static function buildFromTimestamp(int $timestamp): DateTimeInterface
    {
        return DateTime::createFromFormat('U', $timestamp);
    }

    public static function forEachMonthInYear(
        DateTimeInterface $dateYear,
        callable $callback
    ): void {
        $interval = new DateInterval('P1M');
        $dateStart = (clone $dateYear)->modify(
            'first day of january this year'
        );
        $dateEnd = (clone $dateYear)->modify('last day of december this year');
        $period = new DatePeriod($dateStart, $interval, $dateEnd);

        foreach ($period as $dateMonth) {
            $callback($dateMonth);
        }
    }

    public static function getMonthKey(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m');
    }

    public static function isInMonth(
        DateTimeInterface $dateSearch,
        DateTimeInterface $dateMonth
    ): bool {
        $dateStart = DateHelper::startOfMonth($dateMonth);
        $dateEnd = DateHelper::endOfMonth($dateMonth);

        return $dateStart <= $dateSearch && $dateSearch <= $dateEnd;
    }

    public static function startOfMonth(
        DateTimeInterface $date
    ): DateTimeInterface {
        $date = static::startOfDay($date);

        return $date->modify('first day of this month');
    }

    public static function startOfDay(
        DateTimeInterface $date
    ): DateTimeInterface {
        return (clone $date)->setTime(0, 0);
    }

    public static function endOfMonth(
        DateTimeInterface $date
    ): DateTimeInterface {
        $date = static::endOfDay($date);

        return $date->modify('last day of this month');
    }

    public static function endOfDay(
        DateTimeInterface $date
    ): DateTimeInterface {
        return (clone $date)->setTime(23, 59, 59);
    }

    public static function endOfYear(DateTimeInterface $date): DateTimeInterface
    {
        return self::endOfMonth((clone $date)->modify('last day of december'));
    }

    public static function interfaceToDateTime(
        DateTimeInterface $interface
    ): DateTimeInterface {
        $dateTime = new DateTime();

        return $dateTime->setTimestamp($interface->getTimestamp());
    }

    public static function dayOfMonth(
        DateTimeInterface $dateTime,
        int $dayOfMonth
    ): DateTimeInterface {
        return (clone $dateTime)
            ->setDate(
                $dateTime->format(
                    DateHelper::DATE_PATTERN_PART_YEAR_FULL
                ),
                $dateTime->format(
                    DateHelper::DATE_PATTERN_PART_MONTH_FULL
                ),
                $dayOfMonth,
            );
    }

    public static function getDayInt(DateTimeInterface $dateTime): int
    {
        return (int) $dateTime->format('j');
    }

    public static function translateDate(
        DateTimeInterface $dateTime,
        string $format
    ): string {
        return IntlDateFormatter::formatObject(
            $dateTime,
            $format
        );
    }

    public static function getNextYearDateTime(): DateTimeInterface
    {
        // First january of next year.
        $dateAccounting = new DateTime();
        $dateAccounting->modify('+1 year');

        return DateHelper::startOfYear($dateAccounting);
    }

    public static function startOfYear(DateTimeInterface $date): DateTimeInterface
    {
        return self::startOfMonth((clone $date)->modify('first day of january'));
    }

    public static function getCurrentYearDate(): DateTimeInterface
    {
        return DateTime::createFromFormat(
            DateHelper::DATE_PATTERN_DAY_DEFAULT,
            DateHelper::getCurrentYearInt().'-01-01'
        );
    }

    public static function getCurrentYearInt(): int
    {
        return (int) (new DateTime())->format(DateHelper::DATE_PATTERN_PART_YEAR_FULL);
    }

    public static function now(): DateTimeInterface
    {
        return new DateTime();
    }

    public static function buildFromQueryStringDate(?string $value): ?DateTimeInterface
    {
        if ($value) {
            foreach (self::QUERY_STRING_DATE_FORMATS as $format) {
                $dateTime = DateTime::createFromFormat($format, $value);
                if ($dateTime !== false &&
                    $dateTime->format($format) == $value) {
                    // Check if day is missing
                    if (!str_contains($format, self::DATE_PATTERN_PART_DAY_FULL)) {
                        $dateTime->setDate(
                            $dateTime->format(self::DATE_PATTERN_PART_YEAR_FULL),
                            $dateTime->format(self::DATE_PATTERN_PART_MONTH_FULL),
                            1
                        );
                    }
                    // Check if month is missing
                    if (!str_contains($format, self::DATE_PATTERN_PART_MONTH_FULL)) {
                        $dateTime->setDate(
                            $dateTime->format(self::DATE_PATTERN_PART_YEAR_FULL),
                            1,
                            1
                        );
                    }
                    // Check if time is missing
                    if (!str_contains($format, self::DATE_PATTERN_PART_HOURS_FULL)) {
                        $dateTime->setTime(0, 0);
                    } elseif (!str_contains($format, self::DATE_PATTERN_PART_MINUTES_FULL)) {
                        // if the format contains hour but not minutes
                        $dateTime->setTime(
                            $dateTime->format(self::DATE_PATTERN_PART_HOURS_FULL),
                            0
                        );
                    } elseif (!str_contains($format, self::DATE_PATTERN_PART_SECONDS_FULL)) {
                        // if the format contains hour and minutes but not seconds
                        $dateTime->setTime(
                            $dateTime->format(self::DATE_PATTERN_PART_HOURS_FULL),
                            $dateTime->format(self::DATE_PATTERN_PART_MINUTES_FULL)
                        );
                    }
                    return $dateTime;
                }
            }
        }

        return null;
    }

}
