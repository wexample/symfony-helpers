<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractCommand extends Command
{
    protected static $defaultDescription = null;

    public function __construct(
        string $name = null,
    )
    {
        parent::__construct($name ?: $this->buildDefaultName());
    }

    public static function getCommandPrefixGroup(): string
    {
        return VariableHelper::APP;
    }

    public static function buildDefaultName(): string
    {
        return static::getCommandPrefixGroup()
            . ':' . TextHelper::toKebab(
                TextHelper::removeSuffix(
                    ClassHelper::getShortName(static::class),
                    'Command'
                )
            );
    }

    protected function configure(): void
    {
        if (static::$defaultDescription) {
            $this->setDescription(static::$defaultDescription);
        }
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execCommand(
        string $command,
        OutputInterface $output
    ): void
    {
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

    protected function executeAndCatchErrors(
        InputInterface $input,
        OutputInterface $output,
        callable $callback
    ): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            return $callback($input, $output, $io);
        } catch (\Wexample\SymfonyApi\Exception\ValidationException $e) {
            // ValidationException already has a formatted message with error details
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('An unexpected error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
