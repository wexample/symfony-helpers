<?php

namespace Wexample\SymfonyHelpers\Helper;

use Symfony\Component\Routing\RouteCollection;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Controller\AbstractController;

class RouteHelper
{
    public static function buildRoutePrefixFromControllerClass(
        AbstractController|string $controllerClass
    ): string
    {
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
    ): array
    {
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
