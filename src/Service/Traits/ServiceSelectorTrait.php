<?php

namespace Wexample\SymfonyHelpers\Service\Traits;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait ServiceSelectorTrait
{
    private array $servicesSelections = [];

    public function getServiceSelection(
        string $service,
        $group = VariableHelper::DEFAULT
    ): ?object {
        if (!isset($this->servicesSelections[$group])) {
            return null;
        }

        $output = $this->servicesSelections[$group][$service] ?? null;

        // Support selecting with full class name.
        if (is_null($output)) {
            foreach ($this->servicesSelections[$group] as $serviceClass) {
                if ($serviceClass::class === $service) {
                    return $serviceClass;
                }
            }
        }

        return $output;
    }

    public function addServiceSelection(
        array|object $services,
        $group = VariableHelper::DEFAULT
    ): void {
        if (!is_array($services)) {
            $services = [$services];
        }

        foreach ($services as $service) {
            $this->servicesSelections[$group][ClassHelper::getTableizedName($service)]
                = $service;
        }
    }
}