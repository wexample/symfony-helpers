<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Symfony\Component\Console\Input\InputArgument;

trait JsonArgumentCommandTrait
{
    use FilePathCommandTrait;

    protected function addJsonFilePathArgument(): static
    {
        return $this
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'JSON file path (relative to project root)'
            );
    }
}
