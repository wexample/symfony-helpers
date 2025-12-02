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
        if (! file_exists($fullFilePath)) {
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

    /**
     * Write data to a JSON file
     *
     * @param InputInterface $input
     * @param array $data The data to write to the file
     * @param int $options JSON encoding options
     * @return string The full file path where the data was written
     * @throws InvalidArgumentException If the file cannot be written
     */
    protected function writeJsonFile(InputInterface $input, array $data, int $options = 0): string
    {
        $filePath = $input->getArgument('file');
        $fullFilePath = $this->getFullFilePath($filePath);

        // Create directory if it doesn't exist
        $directory = dirname($fullFilePath);
        if (! is_dir($directory)) {
            if (! mkdir($directory, 0777, true) && ! is_dir($directory)) {
                throw new InvalidArgumentException(sprintf('Directory "%s" could not be created', $directory));
            }
        }

        // Default options for better readability
        if ($options === 0) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        }

        // Encode data to JSON
        $jsonContent = json_encode($data, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(sprintf('Failed to encode data to JSON: %s', json_last_error_msg()));
        }

        // Write to file
        if (file_put_contents($fullFilePath, $jsonContent) === false) {
            throw new InvalidArgumentException(sprintf('Failed to write to file %s', $fullFilePath));
        }

        return $fullFilePath;
    }
}
