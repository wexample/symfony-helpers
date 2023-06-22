<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;

abstract class AbstractBundleCommand extends Command
{
    public function __construct(
        protected KernelInterface $kernel,
        string $name = null,
    ) {
        parent::__construct($name ?: $this->buildDefaultName());
    }

    public static function buildDefaultName(): string
    {
        return TextHelper::toKebab(
                TextHelper::removePrefix(
                    TextHelper::removeSuffix(
                        ClassHelper::getShortName(static::getBundleClassName()),
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

    abstract public static function getBundleClassName(): string;

    protected function getBundleRootPath():string
    {
        return realpath($this->getBundle()->getPath() . '/../') . '/';
    }

    protected function getBundle(): BundleInterface
    {
        return $this->kernel->getBundle(
            ClassHelper::getShortName(
                $this::getBundleClassName()
            )
        );
    }
}
