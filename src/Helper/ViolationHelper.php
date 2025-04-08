<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ViolationHelper
{
    public static function getFormattedMessage(ConstraintViolationInterface $violation): string
    {
        $formattedMessage = $violation->getMessage();
        foreach ($violation->getParameters() as $key => $value) {
            $formattedMessage = str_replace($key, $value, $formattedMessage);
        }

        return $formattedMessage;
    }
}
