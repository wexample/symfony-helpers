<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DebugHelper
{
    private const INDENT = '    '; // 4 spaces

    /**
     * Format variable for debug output
     */
    public static function formatVar(mixed $var, int $depth = 0): string
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

            $indent = str_repeat(self::INDENT, $depth);
            $output = [];
            $isSequential = array_keys($var) === range(0, count($var) - 1);

            foreach ($var as $key => $value) {
                $formattedKey = $isSequential ? '' : $key . ': ';
                $output[] = $indent . self::INDENT . $formattedKey . static::formatVar($value, $depth + 1);
            }

            return "[\n" . implode(",\n", $output) . "\n" . $indent . "]";
        }

        if (is_object($var)) {
            $indent = str_repeat(self::INDENT, $depth);
            $className = get_class($var);

            // Special handling for UploadedFile
            if ($var instanceof UploadedFile) {
                return "$className {\n" .
                    $indent . self::INDENT . "originalName: \"" . $var->getClientOriginalName() . "\",\n" .
                    $indent . self::INDENT . "mimeType: \"" . $var->getClientMimeType() . "\",\n" .
                    $indent . self::INDENT . "size: " . $var->getSize() . ",\n" .
                    $indent . self::INDENT . "error: " . $var->getError() . "\n" .
                    $indent . "}";
            }

            $properties = [];

            // Get public properties
            foreach (get_object_vars($var) as $key => $value) {
                $properties[] = $indent . self::INDENT . $key . ": " . static::formatVar($value, $depth + 1);
            }

            // If no public properties but has __toString
            if (empty($properties) && method_exists($var, '__toString')) {
                return "$className {\"" . $var->__toString() . "\"}";
            }

            if (empty($properties)) {
                return "$className {}";
            }

            return "$className {\n" . implode(",\n", $properties) . "\n" . $indent . "}";
        }

        if (is_string($var)) {
            // Detect if string is multiline
            if (str_contains($var, "\n")) {
                $indent = str_repeat(self::INDENT, $depth);
                $lines = explode("\n", $var);
                return "\"\n" . $indent . self::INDENT . implode("\n" . $indent . self::INDENT, $lines) . "\n" . $indent . "\"";
            }
            return "\"" . $var . "\"";
        }

        if (is_numeric($var)) {
            return (string) $var;
        }

        return (string) $var;
    }
}