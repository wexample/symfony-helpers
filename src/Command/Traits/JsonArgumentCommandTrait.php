<?php

namespace Wexample\SymfonyHelpers\Command\Traits;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

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
     * @throws InvalidArgumentException If file does not exist or contains invalid JSON
     */
    protected function readJsonFile(InputInterface $input): array
    {
        $filePath = $input->getArgument('file');
        $fullFilePath = $this->getFullFilePath($filePath);

        // Check if file exists
        if (!file_exists($fullFilePath)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist', $fullFilePath));
        }

        // Read JSON file
        $jsonContent = file_get_contents($fullFilePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(sprintf('Invalid JSON in file %s: %s', $fullFilePath, json_last_error_msg()));
        }

        if (empty($data)) {
            return [];
        }

        return $data;
    }
}
