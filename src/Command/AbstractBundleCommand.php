<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;

abstract class AbstractBundleCommand extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name ?: $this->buildDefaultName());
    }

    public static function buildDefaultName(): string
    {
        return TextHelper::toKebab(
                TextHelper::removePrefix(
                    TextHelper::removeSuffix(
                        ClassHelper::getShortName(static::getBundle()),
                        'Bundle'
                    ),
                    'Wexample'
                )
            )
            .':'
            .TextHelper::toKebab(
                TextHelper::removeSuffix(
                    ClassHelper::getShortName(static::class),
                    'Command'
                )
            );
    }

    abstract public static function getBundle(): string;
}
