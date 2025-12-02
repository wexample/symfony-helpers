<?php

namespace Wexample\SymfonyHelpers\Helper;

class CodeHelper
{
    public static function removeIndentation(string $text): string
    {
        return preg_replace("/\s+/", " ", preg_replace("/\r|\n/", " ", $text));
    }
}
