<?php

namespace App\Trait;

use InvalidArgumentException;
use TypeError;

trait HasEnvironmentConfigurationTrait
{
    private array $parameters = [];
    private array $parameterTypes = [];

    /**
     * Sets a single parameter with type checking
     * @throws TypeError If the type does not match
     */
    public function setParameter(string $name, mixed $value, string $type): void
    {
        if (!in_array($type, ['string', 'int', 'bool', 'float', 'array'])) {
            throw new InvalidArgumentException(
                sprintf('Invalid type "%s" for parameter "%s". Allowed types: string, int, bool, float, array',
                    $type,
                    $name
                )
            );
        }

        $this->validateParameterType($name, $value, $type);

        $this->parameters[$name] = $value;
        $this->parameterTypes[$name] = $type;
    }

    /**
     * Sets multiple parameters at once
     * @param array<string, array{value: mixed, type: string}> $parameters
     */
    public function setParameters(array $parameters): void
    {
        foreach ($parameters as $name => $config) {
            if (!isset($config['value'], $config['type'])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid parameter configuration for "%s". Must contain "value" and "type" keys', $name)
                );
            }

            $this->setParameter($name, $config['value'], $config['type']);
        }
    }

    /**
     * Retrieves a parameter with strong typing
     * @template T
     * @param string $name
     * @return T
     */
    protected function getParameter(string $name): mixed
    {
        if (!isset($this->parameters[$name])) {
            throw new InvalidArgumentException(
                sprintf('Parameter not found: "%s"', $name)
            );
        }

        return $this->parameters[$name];
    }

    /**
     * Checks if a parameter exists
     */
    protected function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Validates the type of parameter
     * @throws TypeError If the type does not match
     */
    private function validateParameterType(string $name, mixed $value, string $expectedType): void
    {
        $actualType = gettype($value);

        // Special handling for integers that may be represented as strings
        if ($expectedType === 'int' && is_string($value) && ctype_digit($value)) {
            return;
        }

        // Converts PHP types to their simple equivalents
        $actualType = match($actualType) {
            'integer' => 'int',
            'boolean' => 'bool',
            'double' => 'float',
            default => $actualType
        };

        if ($actualType !== $expectedType) {
            throw new TypeError(
                sprintf(
                    'Invalid type for parameter "%s". Expected: %s, Received: %s',
                    $name,
                    $expectedType,
                    $actualType
                )
            );
        }
    }
}