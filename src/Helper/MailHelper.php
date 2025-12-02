<?php

namespace Wexample\SymfonyHelpers\Helper;

use function implode;
use function preg_match_all;
use function trim;

class MailHelper
{
    public static function getMailToName(string $to): ?string
    {
        $names = [];

        if (preg_match_all(
            '/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/',
            $to,
            $matches,
            PREG_SET_ORDER
        ) > 0) {
            foreach ($matches as $m) {
                if (! empty($m[2])) {
                    $names[trim($m[2], '<>')] = $m[1];
                } else {
                    $names[$m[1]] = $m[1];
                }
            }
        } else {
            return $to;
        }

        return implode(',', $names);
    }
}
