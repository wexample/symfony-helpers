<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractCommand extends Command
{
    public function __construct(
        string $name = null,
    ) {
        parent::__construct($name ?: $this->buildDefaultName());
    }

    public static function getCommandPrefixGroup(): string
    {
        return VariableHelper::APP;
    }

    public static function buildDefaultName(): string
    {
        return self::getCommandPrefixGroup()
            .':'.TextHelper::toKebab(
                TextHelper::removeSuffix(
                    ClassHelper::getShortName(static::class),
                    'Command'
                )
            );
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execCommand(
        string $command,
        OutputInterface $output
    ): void {
        if (is_subclass_of(
            $command,
            AbstractBundleCommand::class
        )
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
