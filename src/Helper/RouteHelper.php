<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\Routing\RouteCollection;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Controller\AbstractController;

class RouteHelper
{
    public static function getRouteAttributePath(object $routeAttribute): array|string|null
    {
        if (! property_exists($routeAttribute, 'path')) {
            return null;
        }

        try {
            $property = new \ReflectionProperty($routeAttribute, 'path');
        } catch (\ReflectionException) {
            return null;
        }

        if (! $property->isPublic()) {
            $property->setAccessible(true);
        }

        return $property->getValue($routeAttribute);
    }

    public static function getRouteAttributeName(object $routeAttribute): ?string
    {
        if (! property_exists($routeAttribute, 'name')) {
            return null;
        }

        try {
            $property = new \ReflectionProperty($routeAttribute, 'name');
        } catch (\ReflectionException) {
            return null;
        }

        if (! $property->isPublic()) {
            $property->setAccessible(true);
        }

        return $property->getValue($routeAttribute);
    }

    public static function buildRouteNameFromParts(array $parts, string $filename): string
    {
        $parts = array_values(array_filter(
            [
                ...$parts,
                $filename,
            ],
            static fn($part) => $part !== null && $part !== ''
        ));

        $routeName = TextHelper::toSnake(implode('_', $parts));

        return preg_replace('/_+/', '_', $routeName);
    }

    public static function buildRoutePathFromParts(
        array $parts,
        string $filename,
        ?string $basePath = null
    ): string {
        $pathParts = array_values(array_filter(
            $parts,
            static fn($part) => $part !== null && $part !== ''
        ));

        $pathParts = array_map([TextHelper::class, 'toKebab'], $pathParts);
        $suffixPath = implode('/', $pathParts);

        if ($basePath === null) {
            $path = $suffixPath === '' ? '/' : '/' . $suffixPath;
        } else {
            $path = self::normalizeRoutePath($basePath);

            if ($suffixPath !== '') {
                $path = self::combineRoutePaths($path, $suffixPath);
            }
        }

        if ($filename !== AbstractController::DEFAULT_ROUTE_NAME_INDEX) {
            $path = self::combineRoutePaths($path, TextHelper::toKebab($filename));
        }

        return self::normalizeRoutePath($path);
    }

    public static function buildRouteNameFromPath(string $fullPath): string
    {
        $trimmedPath = trim($fullPath, '/');

        if ($trimmedPath === '') {
            return 'index';
        }

        $routeName = str_replace(['/', '-'], '_', $trimmedPath);
        $routeName = TextHelper::toSnake($routeName);

        return preg_replace('/_+/', '_', $routeName);
    }

    public static function normalizeRoutePath(string $path): string
    {
        $normalized = '/' . ltrim(trim($path), '/');

        return rtrim($normalized, '/');
    }

    public static function combineRoutePaths(string $basePath, string $path): string
    {
        if ($path === '') {
            return $basePath;
        }

        if ($basePath === '' || str_starts_with($path, '/')) {
            return $path;
        }

        return rtrim($basePath, '/') . '/' . ltrim($path, '/');
    }

    public static function buildRoutePrefixFromControllerClass(
        AbstractController|string $controllerClass
    ): string {
        return TextHelper::toSnake(
            AbstractController::removeSuffix(
                ClassHelper::getShortName(
                    $controllerClass
                )
            )
        ) . '_';
    }

    public static function filterRouteByController(
        RouteCollection $allRoutes,
        string $controllerClass
    ): array {
        $routes = [];

        foreach ($allRoutes as $name => $route) {
            $defaults = $route->getDefaults();

            if (isset($defaults['_controller'])) {
                // Extract the controller class name from _controller
                // Typical format: 'App\Controller\MyController::myMethod'
                $routeControllerClass = ClassHelper::getClassPath($defaults['_controller']);

                if ($routeControllerClass === $controllerClass) {
                    $routes[$name] = $route;
                }
            }
        }

        return $routes;
    }
}
