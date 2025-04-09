<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DebugHelper
{
    private const INDENT = '· '; // Need to add a non-space first char to avoid request tools to trim lines.

    /**
     * Format variable for debug output
     */
    public static function formatVar(
        mixed $var,
        int $depth = 0
    ): string {
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
                $formattedKey = $isSequential ? '' : $key.': ';
                $output[] = $indent.self::INDENT.$formattedKey.static::formatVar($value, $depth + 1);
            }

            return "[\n".implode(",\n", $output)."\n".$indent."]";
        }

        if (is_object($var)) {
            $indent = str_repeat(self::INDENT, $depth);
            $className = get_class($var);

            // Special handling for UploadedFile
            if ($var instanceof UploadedFile) {
                return "$className {\n".
                    $indent.self::INDENT."originalName: \"".$var->getClientOriginalName()."\",\n".
                    $indent.self::INDENT."mimeType: \"".$var->getClientMimeType()."\",\n".
                    $indent.self::INDENT."size: ".$var->getSize().",\n".
                    $indent.self::INDENT."error: ".$var->getError()."\n".
                    $indent."}";
            }

            $properties = [];

            // Get public properties
            foreach (get_object_vars($var) as $key => $value) {
                $properties[] = $indent.self::INDENT.$key.": ".static::formatVar($value, $depth + 1);
            }

            // If no public properties but has __toString
            if (empty($properties) && method_exists($var, '__toString')) {
                return "$className {\"".$var->__toString()."\"}";
            }

            if (empty($properties)) {
                return "$className {}";
            }

            return "$className {\n".implode(",\n", $properties)."\n".$indent."}";
        }

        if (is_string($var)) {
            // Detect if string is multiline
            if (str_contains($var, "\n")) {
                $indent = str_repeat(self::INDENT, $depth);
                $lines = explode("\n", $var);
                return "\"\n".$indent.self::INDENT.implode("\n".$indent.self::INDENT, $lines)."\n".$indent."\"";
            }
            return "\"".$var."\"";
        }

        if (is_numeric($var)) {
            return (string) $var;
        }

        return (string) $var;
    }

    /**
     * Format an exception trace array into a readable string
     *
     * @param array $trace The trace array from Exception::getTrace()
     * @param bool $ignoreArgs If true, function arguments will not be included in the output
     * @param int $maxFrames Maximum number of frames to display (0 = all)
     * @return string Formatted trace string
     */
    public static function formatTrace(array $trace, bool $ignoreArgs = false, int $maxFrames = 0): string
    {
        $result = "";
        $frameCount = count($trace);
        $limit = $maxFrames > 0 ? min($frameCount, $maxFrames) : $frameCount;

        for ($i = 0; $i < $limit; $i++) {
            $frame = $trace[$i];
            $file = $frame['file'] ?? '(unknown file)';
            $line = $frame['line'] ?? '(unknown line)';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';
            $function = $frame['function'] ?? '(unknown function)';

            $result .= sprintf("#%d %s:%d %s%s%s(", $i, $file, $line, $class, $type, $function);

            // Add function arguments if requested
            if (!$ignoreArgs && isset($frame['args']) && is_array($frame['args'])) {
                $argsStr = [];
                foreach ($frame['args'] as $arg) {
                    if (is_object($arg)) {
                        $argsStr[] = get_class($arg);
                    } elseif (is_array($arg)) {
                        $argsStr[] = 'Array(' . count($arg) . ')';
                    } elseif (is_string($arg)) {
                        $argStr = strlen($arg) > 50 ? substr($arg, 0, 47) . '...' : $arg;
                        $argsStr[] = "'" . addslashes($argStr) . "'";
                    } elseif (is_null($arg)) {
                        $argsStr[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $argsStr[] = $arg ? 'true' : 'false';
                    } elseif (is_resource($arg)) {
                        $argsStr[] = get_resource_type($arg);
                    } else {
                        $argsStr[] = (string)$arg;
                    }
                }
                $result .= implode(', ', $argsStr);
            } else if (!$ignoreArgs) {
                $result .= '...';
            }

            $result .= ")\n";
        }

        // Add a note if we truncated the trace
        if ($maxFrames > 0 && $frameCount > $maxFrames) {
            $result .= sprintf("... %d more frames omitted ...\n", $frameCount - $maxFrames);
        }

        return $result;
    }
}