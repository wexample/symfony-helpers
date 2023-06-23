<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wexample\SymfonyHelpers\Helper\JsonHelper;

abstract class AbstractCheckNodeInstallCommand extends AbstractBundleCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Check dependencies installation');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $bundleRootPath = $this->bundleService->getBundleRootPath(
            $this->getBundleClassName()
        );
        $packageJsonPath = $bundleRootPath.'package.json';

        if (!file_exists($packageJsonPath)) {
            $io->error('No package.json file found.');

            return Command::FAILURE;
        }

        $packageJsonContent = file_get_contents($packageJsonPath);
        $packageJsonData = json_decode($packageJsonContent, true);
        $dependencyFile = $bundleRootPath.'package.dependencies.json';

        if (!is_file($dependencyFile)) {
            $io->error('Missing file : '.$dependencyFile);
            return Command::FAILURE;
        }

        $neededDependencies = JsonHelper::read(
            $dependencyFile,
        );

        $missingDependencies = [];

        foreach ($neededDependencies->dependencies as $dependency) {
            if (!isset($packageJsonData['dependencies'][$dependency]) && !isset($packageJsonData['devDependencies'][$dependency])) {
                $missingDependencies[] = $dependency;
            }
        }

        if (!empty($missingDependencies)) {
            $missingDependenciesString = implode(' ', $missingDependencies);
            $io->error("Missing node modules: '{$missingDependenciesString}'. Run `npm install {$missingDependenciesString}` or `yarn add {$missingDependenciesString}`.");

            return Command::FAILURE;
        }

        $io->success('All dependencies are installed.');

        return Command::SUCCESS;
    }
}
