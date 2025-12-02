<?php

namespace Wexample\SymfonyHelpers\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

abstract class AbstractRouteLoader extends Loader
{
    protected bool $isLoaded = false;

    public function __construct(
        protected ContainerInterface $container,
        string $env = null
    ) {
        parent::__construct($env);
    }

    public function load(
        mixed $resource,
        ?string $type = null
    ): mixed {
        if ($this->isLoaded) {
            throw new \RuntimeException('CustomRouteLoader already loaded.');
        }

        $collection = $this->loadOnce(
            $resource,
            $type
        );

        $this->isLoaded = true;

        return $collection;
    }

    abstract protected function loadOnce(
        $resource,
        string $type = null
    ): RouteCollection;

    abstract protected function getName(): string;

    public function supports(
        $resource,
        string $type = null
    ): bool {
        return $type === $this->getName();
    }

    protected function getAllControllersClasses(): array
    {
        $controllers = [];
        $serviceIds = $this->container->getServiceIds();

        foreach ($serviceIds as $serviceId) {
            if (str_contains($serviceId, 'Controller') && class_exists($serviceId)) {
                $controllers[] = new \ReflectionClass($serviceId);
            }
        }

        return $controllers;
    }

    protected function getAllControllersClassesWithAttribute(string $attributeClass): array
    {
        $controllersWithAttribute = [];

        foreach ($this->getAllControllersClasses() as $reflectionClass) {
            $attributes = $reflectionClass->getAttributes($attributeClass);

            if (! empty($attributes)) {
                $controllersWithAttribute[$reflectionClass->getName()] = [
                    'reflection' => $reflectionClass,
                    'attribute' => $attributes[0]->newInstance(),
                ];
            }
        }

        return $controllersWithAttribute;
    }
}
