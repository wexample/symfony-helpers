<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Helper\JsonHelper;
use Wexample\SymfonyHelpers\Service\BundleService;

abstract class AbstractCheckNodeInstallCommand extends AbstractBundleCommand
{
    public function __construct(
        BundleService $bundleService,
        private KernelInterface $kernel,
        string $name = null
    ) {
        parent::__construct(
            $bundleService,
            $name
        );
    }

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

        $packageJsonPath = $this->kernel->getProjectDir().'/package.json';
        if (!file_exists($packageJsonPath)) {
            $io->error('No package.json file found in '.$packageJsonPath);

            return Command::FAILURE;
        }

        $packageJsonContent = file_get_contents($packageJsonPath);
        $packageJsonData = json_decode($packageJsonContent, true);
        $bundleRootPath = $this->bundleService->getBundleRootPath(
            $this->getBundleClassName()
        );
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
