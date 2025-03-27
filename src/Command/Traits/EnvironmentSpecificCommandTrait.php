<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait EnvironmentSpecificCommandTrait
{
    protected ParameterBagInterface $parameterBag;

    /**
     * @required
     */
    public function setParameterBag(ParameterBagInterface $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    abstract protected function getSupportedEnvExecution(): array;

    /**
     * Checks if the current environment is allowed for command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool True if environment is allowed, false otherwise
     */
    protected function isEnvironmentAllowed(
        InputInterface $input,
        OutputInterface $output
    ): bool
    {
        $currentEnv = $this->parameterBag->get('kernel.environment');
        $allowedEnvs = $this->getSupportedEnvExecution();

        if (!in_array($currentEnv, $allowedEnvs)) {
            $io = new SymfonyStyle($input, $output);
            $io->error(sprintf(
                'This command can only be executed in the following environments: %s',
                implode(', ', $allowedEnvs)
            ));
            $io->error(sprintf(
                'Current environment is: %s',
                $currentEnv
            ));
            return false;
        }

        return true;
    }
}
