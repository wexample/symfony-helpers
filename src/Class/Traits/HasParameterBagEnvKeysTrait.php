<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait HasParameterBagEnvKeysTrait
{
    use HasEnvKeysTrait;

    protected readonly ParameterBagInterface $parameterBag;

    public function setParameterBag(ParameterBagInterface $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Override to fetch missing environment keys using Symfony's ParameterBagInterface.
     *
     * @param array $expectedEnvKeys
     * @return array
     */
    private function getMissingEnvKeys(array $expectedEnvKeys): array
    {
        $missingKeys = [];

        foreach ($expectedEnvKeys as $key) {
            // Check if the environment variable exists in the Symfony parameter bag
            if (!$this->parameterBag->has($key)) {
                $missingKeys[] = $key;
            }
        }

        return $missingKeys;
    }

    public function getEnvParameter(string $key): mixed
    {
        if (!$this->parameterBag->has($key)) {
            throw new \InvalidArgumentException(sprintf('Environment parameter "%s" is not defined.', $key));
        }

        return $this->parameterBag->get($key);
    }
}
