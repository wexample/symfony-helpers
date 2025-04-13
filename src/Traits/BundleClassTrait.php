<?php

namespace Wexample\SymfonyHelpers\Traits;

use Symfony\Component\HttpKernel\Bundle\Bundle;

trait BundleClassTrait
{
    /**
     * @return Bundle|string
     */
    abstract public static function getBundleClassName(): string;
}
