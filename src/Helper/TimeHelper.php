<?php

namespace Wexample\SymfonyHelpers\Helper;

class TimeHelper
{
    public static function calcMicroTime(\DateTimeInterface $dateTime): float
    {
        return floatval($dateTime->format('U.u'));
    }

    public static function formatMicroTimeDiff(float $timeDiff): string
    {
        if($timeDiff <= 0) {
            throw new \InvalidArgumentException('Time difference must be greater than 0');
        }

        $hours = (int) ($timeDiff / 3600);
        $timeDiff -= $hours * 3600;

        $minutes = (int) ($timeDiff / 60);
        $timeDiff -= $minutes * 60;

        $seconds = (int) $timeDiff;
        $microSeconds = bcmul(($timeDiff - $seconds), "1000000");

        return sprintf("%02d:%02d:%02d.%06d", $hours, $minutes, $seconds, (int)$microSeconds);
    }

    public static function getMicroTimeDiff(
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateEnd
    ): string {
        if($dateStart > $dateEnd) {
            throw new \InvalidArgumentException('$dateStart must be earlier than $dateEnd');
        }

        $microTimeA = self::calcMicroTime($dateStart);
        $microTimeB = self::calcMicroTime($dateEnd);

        return self::formatMicroTimeDiff(
            $microTimeB - $microTimeA
        );
    }
}
