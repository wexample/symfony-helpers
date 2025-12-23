<?php

namespace Wexample\SymfonyHelpers\Helper;

use function htmlspecialchars;
use function implode;
use function is_array;
use function is_float;
use function is_null;
use function preg_replace;
use function str_replace;
use function trim;

use Wexample\Helpers\Helper\TextHelper;

class DomHelper
{
    final public const ATTRIBUTE_STYLE = 'style';

    final public const ATTRIBUTE_CLASS = 'class';

    final public const CHAR_NBSP = '&nbsp;';

    final public const CSS_RULE_FONT_SIZE = 'font-size';

    final public const TAG_DIV = 'div';

    final public const TAG_SPAN = 'span';

    final public const TAG_LINK = 'link';

    final public const TAG_ALLOWS_AUTO_CLOSING = [
        self::TAG_DIV => false,
        self::TAG_SPAN => false,
    ];

    public static function buildTag(
        string $tagName,
        array $attributes = [],
        string $body = '',
        bool $allowSingleTag = null
    ): string {
        $output = '<'.$tagName;

        $outputAttributes = static::buildTagAttributes($attributes);
        if ('' !== $outputAttributes) {
            $output .= ' '.$outputAttributes;
        }

        if (is_null($allowSingleTag)) {
            $allowSingleTag = static::TAG_ALLOWS_AUTO_CLOSING[$tagName] ?? false;
        }

        if ($allowSingleTag && '' === $body) {
            return $output.'/>';
        }

        return $output.'>'.$body.'</'.$tagName.'>';
    }

    public static function buildTagAttributes(array $attributes): string
    {
        return self::arrayToAttributes($attributes ?: []);
    }

    public static function arrayToAttributes(array $array): string
    {
        $output = [];
        foreach ($array as $key => $value) {
            if (null !== $value) {
                if (self::ATTRIBUTE_STYLE === $key && is_array($value)) {
                    $style = [];

                    foreach ($value as $styleKey => $styleValue) {
                        if (is_float($styleValue)) {
                            $styleValue .= 'px';
                        }

                        $style[] = $styleKey.':'.$styleValue;
                    }

                    $value = implode($style);
                }

                $output[] = str_replace(
                    '_',
                    '-',
                    TextHelper::camelToDash($key)
                )
                    .'="'.htmlspecialchars($value).'"';
            }
        }

        return implode(' ', $output);
    }

    public static function buildStringIdentifier(string $string): string
    {
        return trim(
            // Replace double dash.
            preg_replace(
                '/-+/',
                '-',
                // Keep valid chars.
                TextHelper::toKebab(
                    preg_replace(
                        '/[^a-zA-Z0-9-]/',
                        '-',
                        $string
                    )
                )
            ),
            '-'
        );
    }
}
