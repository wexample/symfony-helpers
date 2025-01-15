<?php

namespace Wexample\SymfonyHelpers\Helper;

class DebugHelper
{
    /**
     * Format variable for debug output
     */
    public static function formatVar(mixed $var): string
    {
        if (is_null($var)) {
            return 'NULL';
        }

        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }

        if (is_array($var)) {
            $output = [];
            foreach ($var as $key => $value) {
                $output[] = $key . ': ' . static::formatVar($value);
            }
            return "[\n  " . implode(",\n  ", $output) . "\n]";
        }

        if (is_object($var)) {
            return get_class($var) . ' ' . json_encode($var, JSON_PRETTY_PRINT);
        }

        if (is_string($var)) {
            return '"' . $var . '"';
        }

        return (string) $var;
    }
}