<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits;

use Symfony\Component\Console\Tester\CommandTester;

trait CommandTestCaseTrait
{
    use ApplicationTestCaseTrait;

    protected function createCommandTester(string $command): CommandTester
    {
        $application = $this->createApplication();

        if (class_exists($command)) {
            $command = $command::buildDefaultName();
        }

        $command = $application->find($command);

        return new CommandTester($command);
    }
}
