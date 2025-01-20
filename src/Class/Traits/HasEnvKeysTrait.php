<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use Wexample\SymfonyHelpers\Helper\EnvironmentHelper;
use Wexample\SymfonyHelpers\Error\MissingRequiredEnvVarError;

trait HasEnvKeysTrait
{
    protected function getExpectedEnvKeys(): array
    {
        return [];
    }

    /**
     * @throws MissingRequiredEnvVarError If any required variable is missing.
     */
    protected function validateEnvKeys(): void
    {
        $missingKeys = EnvironmentHelper::getMissingEnvKeys(
            $this->getExpectedEnvKeys()
        );

        if (!empty($missingKeys)) {
            throw new MissingRequiredEnvVarError($missingKeys);
        }
    }
}
