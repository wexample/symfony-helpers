<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    /**
     * Read and decode JSON file
     */
    protected function readJsonFile(InputInterface $input, OutputInterface $output): ?array
    {
        $filePath = $input->getArgument('file');
        $fullFilePath = $this->getFullFilePath($filePath);

        $io = new SymfonyStyle($input, $output);
        // Check if file exists
        if (!file_exists($fullFilePath)) {
            $io->error(sprintf('File %s does not exist', $fullFilePath));
            return null;
        }

        // Read JSON file
        $jsonContent = file_get_contents($fullFilePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error(sprintf('Invalid JSON in file %s: %s', $fullFilePath, json_last_error_msg()));
            return null;
        }

        if (empty($data)) {
            $io->warning('No data found in the JSON file');
            return [];
        }

        return $data;
    }
}
