<?php

namespace Wexample\SymfonyHelpers\Helper;

class JsonHelper
{
    public static function read(
        string $path,
        bool $associative = null,
        int $flags = 0,
        array|object|null $default = null
    ): array|object|null {
        if (is_file($path)) {
            return json_decode(
                file_get_contents(
                    $path
                ),
                associative: $associative,
                flags: $flags
            ) ?? $default;
        }

        return $default;
    }

    public static function write(
        string $path,
        array|object $data,
        int $flags = 0,
        int $depth = 512,
        bool $newLine = true
    ): bool {
        $json = json_encode(
            $data,
            flags: $flags,
            depth: $depth
        );

        return $json && file_put_contents($path, $json.($newLine ? PHP_EOL : ''));
    }

    public static function isJson(mixed $string): bool
    {
        return json_validate($string);
    }

    /**
     * Read a JSON file and decode its contents, returning null if any error occurs
     *
     * This function is designed to be used in contexts where you want to avoid
     * try/catch blocks for handling file reading or JSON decoding errors.
     *
     * @param string $path Path to the JSON file
     * @param bool|null $associative When true, returned objects will be converted into associative arrays
     * @param int $flags JSON decode flags
     * @return array|object|null Decoded JSON data or null if file doesn't exist or contains invalid JSON
     */
    public static function readOrNull(
        string $path,
        bool $associative = null,
        int $flags = 0
    ): array|object|null {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, $associative, 512, $flags);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }
}
