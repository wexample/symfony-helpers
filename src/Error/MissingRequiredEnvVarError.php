<?php

namespace Wexample\SymfonyHelpers\Error;

use Exception;

class MissingRequiredEnvVarError extends Exception
{
    private array $missingKeys;

    public function __construct(array $missingKeys)
    {
        $this->missingKeys = $missingKeys;
        $message = "Missing required environment variables: " . implode(', ', $missingKeys);
        parent::__construct($message);
    }

    public function getMissingKeys(): array
    {
        return $this->missingKeys;
    }
}
