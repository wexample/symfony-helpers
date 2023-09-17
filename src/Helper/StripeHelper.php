<?php

namespace Wexample\SymfonyHelpers\Helper;

use function hash_hmac;

class StripeHelper
{
    public static function buildFakeSignature(
        string $payload,
        string $secret
    ): string {
        $timestamp = time();
        $signedPayload = "{$timestamp}.{$payload}";

        return implode(',', [
            't='.$timestamp,
            'v1='.hash_hmac(
                'sha256',
                $signedPayload,
                $secret
            ), ]);
    }
}
