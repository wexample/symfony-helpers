<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TypeError;

trait HasEnvironmentConfigurationTrait
{
    private array $parameters = [];
    private array $parameterTypes = [];

    /**
     * Automatically sets parameters from class properties marked with #[Autowire]
     */
    protected function initializeParameters(): void
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Autowire::class);
            if (empty($attributes)) {
                continue;
            }

            $attribute = $attributes[0];
            $autowireValue = $attribute->getArguments()[0];

            // Extract parameter name from the autowire value (e.g., '%rabbitmq_host%' -> 'rabbitmq_host')
            if (preg_match('/^%(.+)%$/', $autowireValue, $matches)) {
                $paramName = $matches[1];
                $value = $property->getValue($this);

                $this->setParameter($paramName, $value);
            }
        }
    }
    /**
     * Sets a single parameter
     */
    public function setParameter(string $name, mixed $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Sets multiple parameters at once
     * @param array<string, mixed> $parameters
     * You can pass either an array with keys/values or with keys/values and an optional type
     * Example: ['param1' => 'value1', 'param2' => ['value' => 123, 'type' => 'int']]
     */
    public function setParameters(array $parameters): void
    {
        foreach ($parameters as $name => $config) {
            // If the value is an array with 'value' and 'type', handle it
            if (is_array($config)) {
            if (!isset($config['value'])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid parameter configuration for "%s". Must contain "value" key', $name)
                );

            }
            $type = $config['type'] ?? null;  // Type is optional
            $this->setParameter($name, $config['value'], $type);
            } else {
                // If it's just a value, treat it as no type specified
                $this->setParameter($name, $config);
            }
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