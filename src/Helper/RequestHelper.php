<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\HttpFoundation\Response;

class RequestHelper
{
    final public const URL_PART_SEPARATOR = '/';
    final public const URL_QUERY_STRING_EQUAL = '=';

    final public const STATUS_CODES_REDIRECTIONS = [
        Response::HTTP_FOUND,
        Response::HTTP_MOVED_PERMANENTLY,
    ];

    public static function buildQueryStringPartIfNotEmpty(array $parameters): string
    {
        if (!empty($parameters)) {
            return '?' . RequestHelper::buildQueryString($parameters);
        }

        return '';
    }

    public static function buildQueryString(array $parameters): string
    {
        $output = [];

        foreach ($parameters as $key => $value) {
            $output[] = $key.self::URL_QUERY_STRING_EQUAL.urlencode($value);
        }

        return implode('&', $output);
    }

    public static function parseRequestValue(string|float|int|array $value): string|float|int|array
    {
        if (is_numeric($value)) {
            if (str_contains($value, '.')) {
                return (float) $value;
            }

            return (int) $value;
        }

        return $value;
    }
}
