<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DebugHelper
{
    private const INDENT = '│   '; // Pipe + 3 spaces
    private const INDENT_LAST = '└── '; // Corner + 2 dashes + space
    private const INDENT_ITEM = '├── '; // Tee + 2 dashes + space

    /**
     * Format variable for debug output
     */
    public static function formatVar(mixed $var, int $depth = 0, bool $isLast = true): string
    {
        if (is_null($var)) {
            return 'NULL';
        }

        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }

        if (is_array($var)) {
            if (empty($var)) {
                return "[]";
            }

            $output = [];
            $isSequential = array_keys($var) === range(0, count($var) - 1);
            $lastKey = array_key_last($var);

            foreach ($var as $key => $value) {
                $prefix = str_repeat(self::INDENT, $depth);
                $isLastItem = $key === $lastKey;
                $itemPrefix = $isLastItem ? self::INDENT_LAST : self::INDENT_ITEM;
                $formattedKey = $isSequential ? '' : $key . ': ';
                $output[] = $prefix . $itemPrefix . $formattedKey . static::formatVar($value, $depth + 1, $isLastItem);
            }

            return "[\n" . implode("\n", $output) . "\n" . str_repeat(self::INDENT, $depth) . "]";
        }

        if (is_object($var)) {
            $className = get_class($var);
            $prefix = str_repeat(self::INDENT, $depth);

            // Special handling for UploadedFile
            if ($var instanceof UploadedFile) {
                return "$className {\n" .
                    $prefix . self::INDENT_ITEM . "originalName: \"" . $var->getClientOriginalName() . "\",\n" .
                    $prefix . self::INDENT_ITEM . "mimeType: \"" . $var->getClientMimeType() . "\",\n" .
                    $prefix . self::INDENT_ITEM . "size: " . $var->getSize() . ",\n" .
                    $prefix . self::INDENT_LAST . "error: " . $var->getError() . "\n" .
                    $prefix . "}";
            }

            $properties = [];
            $vars = get_object_vars($var);
            $lastKey = array_key_last($vars);

            foreach ($vars as $key => $value) {
                $isLastItem = $key === $lastKey;
                $itemPrefix = $isLastItem ? self::INDENT_LAST : self::INDENT_ITEM;
                $properties[] = $prefix . $itemPrefix . $key . ": " . static::formatVar($value, $depth + 1, $isLastItem);
            }

            if (empty($properties) && method_exists($var, '__toString')) {
                return "$className {\"" . $var->__toString() . "\"}";
            }

            if (empty($properties)) {
                return "$className {}";
            }

            return "$className {\n" . implode("\n", $properties) . "\n" . $prefix . "}";
        }

        if (is_string($var)) {
            if (str_contains($var, "\n")) {
                $prefix = str_repeat(self::INDENT, $depth);
                $lines = explode("\n", $var);
                $lastKey = array_key_last($lines);
                $formattedLines = [];

                foreach ($lines as $key => $line) {
                    $isLastItem = $key === $lastKey;
                    $itemPrefix = $isLastItem ? self::INDENT_LAST : self::INDENT_ITEM;
                    $formattedLines[] = $prefix . $itemPrefix . $line;
                }

                return "\"\n" . implode("\n", $formattedLines) . "\n" . $prefix . "\"";
            }
            return "\"" . $var . "\"";
        }

        return (string) $var;
    }
}