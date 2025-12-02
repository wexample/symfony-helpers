<?php

namespace Wexample\SymfonyHelpers\Helper;

use function htmlspecialchars;
use function implode;
use function is_array;
use function is_float;
use function str_replace;

use Wexample\Helpers\Helper\TextHelper;

class DomHelper
{
    final public const ATTRIBUTE_STYLE = 'style';

    final public const ATTRIBUTE_CLASS = 'class';

    final public const CHAR_NBSP = '&nbsp;';

    final public const CSS_RULE_FONT_SIZE = 'font-size';

    public static function buildTag(
        string $tagName,
        array $attributes
    ): string {
        return '<'.$tagName.' '.
            self::arrayToAttributes($attributes).'>'.
            '</'.$tagName.'>';
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
}
