<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Helper\JsonHelper;
use Wexample\SymfonyHelpers\Service\BundleService;

abstract class AbstractCheckNodeInstallCommand extends AbstractBundleCommand
{
    public function __construct(
        private readonly KernelInterface $kernel,
        BundleService $bundleService,
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
        $bundleRootPath = BundleHelper::getBundleRootPath(
            $this->getBundleClassName(),
            $this->kernel
        );

        $dependencyFile = $bundleRootPath.'assets/package.json';

        if (!is_file($dependencyFile)) {
            $io->error('Missing file : '.$dependencyFile);
            return Command::FAILURE;
        }

        $neededDependencies = JsonHelper::read(
            $dependencyFile,
            JSON_OBJECT_AS_ARRAY
        );

        $missingDependencies = [];

        $dependencies = $neededDependencies['dependencies'] ?? [];
        $dependencies += $neededDependencies['devDependencies'] ?? [];
        $dependencies += $neededDependencies['peerDependencies'] ?? [];

        foreach ($dependencies as $dependency => $version) {
            $io->info('Checking '.$dependency.':'.$version);
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
