<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\Routing\RouteCollection;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Controller\AbstractController;

class RouteHelper
{
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
                $parts = explode('::', $defaults['_controller']);
                $routeControllerClass = $parts[0];

                if ($routeControllerClass === $controllerClass) {
                    $routes[$name] = $route;
                }
            }
        }

        return $routes;
    }
}
