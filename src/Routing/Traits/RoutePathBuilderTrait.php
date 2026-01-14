<?php

namespace Wexample\SymfonyHelpers\Routing\Traits;

use ReflectionClass;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\RouteHelper;

/**
 * Trait providing common route path building functionality for route loaders.
 */
trait RoutePathBuilderTrait
{
    /**
     * Build a route path by combining the base path from the controller's Route attribute
     * with a route name part.
     *
     * @param object $controller The controller object or class
     * @param string $routeNamePart The route name part to append to the base path
     * @return string|null The full route path or null if no Route attribute is found
     */
    protected function buildRoutePathFromController(object $controller, string $routeNamePart): ?string
    {
        $reflectionClass = new ReflectionClass($controller);
        $routeAttributes = $reflectionClass->getAttributes(Route::class);

        if (empty($routeAttributes)) {
            return null;
        }

        $routeAttribute = $routeAttributes[0]->newInstance();
        $basePath = $routeAttribute->getPath();

        // Convert route name part to kebab-case for URL path
        $routePathPart = TextHelper::toKebab($routeNamePart);

        // Combine base path with route path part
        $fullPath = rtrim($basePath, '/') . '/' . $routePathPart;

        return $fullPath;
    }

    /**
     * Build a route name by combining the base name from the controller's Route attribute
     * with a route name part.
     *
     * @param object $controller The controller object or class
     * @param string $routeNamePart The route name part to append to the base name
     * @return string|null The full route name or null if no Route attribute is found
     */
    protected function buildRouteNameFromController(object $controller, string $routeNamePart): ?string
    {
        $reflectionClass = new ReflectionClass($controller);
        $routeAttributes = $reflectionClass->getAttributes(Route::class);

        if (empty($routeAttributes)) {
            return null;
        }

        $routeAttribute = $routeAttributes[0]->newInstance();
        $baseName = $routeAttribute->getName();

        // Combine base name with route name part
        return $baseName . $routeNamePart;
    }

    /**
     * Build a route name based on the computed full path.
     */
    protected function buildRouteNameFromPath(string $fullPath): string
    {
        return RouteHelper::buildRouteNameFromPath($fullPath);
    }
}
