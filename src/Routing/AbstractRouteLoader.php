<?php

namespace Wexample\SymfonyHelpers\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

abstract class AbstractRouteLoader extends Loader
{
    protected bool $isLoaded = false;

    public function load(
        mixed $resource,
        ?string $type = null
    ) {
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
}