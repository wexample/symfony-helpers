<?php

namespace Wexample\SymfonyHelpers\Helper;

class JsonHelper
{
    public static function read(
        string $path,
        ?bool $associative = null,
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
        int $depth = 512
    ): bool {
        $json = json_encode(
            $data,
            flags: $flags,
            depth: $depth
        );

        return $json && file_put_contents($path, $json);
    }

    public static function isJson(mixed $string): bool
    {
        json_decode($string);

        return JSON_ERROR_NONE === json_last_error();
    }
}