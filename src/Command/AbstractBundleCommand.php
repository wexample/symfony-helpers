<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Service\BundleService;

abstract class AbstractBundleCommand extends Command
{
    public function __construct(
        protected BundleService $bundleService,
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

    protected function execCommand(
        string $command,
        OutputInterface $output
    ): void {
        if (is_subclass_of(
            $command,
            AbstractBundleCommand::class)
        ) {
            $command = $command::buildDefaultName();
        }

        $this
            ->getApplication()
            ->find($command)
            ->run(
                new ArrayInput([]),
                $output,
            );
    }

}
