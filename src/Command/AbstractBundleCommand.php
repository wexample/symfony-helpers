<?php

namespace Wexample\SymfonyHelpers\Command;

use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Service\BundleService;

abstract class AbstractBundleCommand extends AbstractCommand
{
    public function __construct(
        protected BundleService $bundleService,
        string $name = null,
    ) {
        parent::__construct(
            $name
        );
    }

    abstract public static function getBundleClassName(): string;

    public static function getCommandPrefixGroup(): string
    {
        return TextHelper::toKebab(
            TextHelper::removePrefix(
                TextHelper::removeSuffix(
                    ClassHelper::getShortName(static::getBundleClassName()),
                    'Bundle'
                ),
                'WexampleSymfony'
            )
        );
    }
}
