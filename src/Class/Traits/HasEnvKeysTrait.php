<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use InvalidArgumentException;
use Wexample\SymfonyHelpers\Error\MissingRequiredEnvVarError;
use Wexample\SymfonyHelpers\Helper\EnvironmentHelper;

trait HasEnvKeysTrait
{
    abstract protected function getExpectedEnvKeys(): array;

    /**
     * @throws MissingRequiredEnvVarError If any required variable is missing.
     */
    protected function validateEnvKeys(): void
    {
        $missingKeys = $this->getMissingEnvKeys(
            $this->getExpectedEnvKeys()
        );

        if (! empty($missingKeys)) {
            throw new MissingRequiredEnvVarError($missingKeys);
        }
    }

    private function getMissingEnvKeys(array $expectedEnvKeys): array
    {
        return EnvironmentHelper::getMissingEnvKeys($expectedEnvKeys);
    }

    /**
     * @throws InvalidArgumentException If the environment variable is not defined.
     */
    public function getEnvParameter(string $key): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            throw new InvalidArgumentException(sprintf('Environment variable "%s" is not defined.', $key));
        }

        return $value;
    }
}
