<?php

namespace Wexample\SymfonyHelpers\Traits;

use Symfony\Component\Console\Output\OutputInterface;
use Wexample\Helpers\Helper\TextHelper;

trait CommandLoggerTrait
{
    use ConsoleLoggerTrait;

    public ?OutputInterface $output = null;

    public function log(
        string $message,
        string $color = TextHelper::ASCII_COLOR_WHITE,
        int $indent = null
    ): void {
        $message = $this->formatLogMessage($message, $color, $indent);

        if ($this->output) {
            $this->output->writeln($message);
        } else {
            echo PHP_EOL.$message;
        }
    }
}
