<?php

namespace Wexample\SymfonyHelpers\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Wexample\SymfonyHelpers\Attribute\SimpleRoutesController;
use Wexample\SymfonyHelpers\Controller\AbstractController;
use Wexample\SymfonyHelpers\Controller\Traits\HasSimpleRoutesControllerTrait;
use Wexample\SymfonyHelpers\Routing\Traits\RoutePathBuilderTrait;

class SimpleRoutesRouteLoader extends AbstractRouteLoader
{
    use RoutePathBuilderTrait;

    protected function isValidSimpleRoutesController(\ReflectionClass $reflectionClass): bool
    {
        $routeAttributes = $reflectionClass->getAttributes(\Symfony\Component\Routing\Annotation\Route::class);

        return ! empty($routeAttributes) &&
            method_exists($reflectionClass->getName(), 'getSimpleRoutes');
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return RouteCollection
     */
    protected function loadOnce(
        $resource,
        string $type = null
    ): RouteCollection {
        $collection = new RouteCollection();

        foreach ($this->getAllControllersClassesWithAttribute(SimpleRoutesController::class) as $controllerName => $data) {
            $reflectionClass = $data['reflection'];

            if (! $this->isValidSimpleRoutesController($reflectionClass)) {
                continue;
            }

            if ($controller = $this->container->get($controllerName)) {
                /** @var HasSimpleRoutesControllerTrait $controller */
                $routes = $controller::getSimpleRoutes();

                foreach ($routes as $routeName) {
                    $fullRouteName = $this->buildRouteNameFromController($controller, $routeName);
                    // "index" route leads to "/" relative path
                    $fullPath = $this->buildRoutePathFromController($controller, $routeName === AbstractController::DEFAULT_ROUTE_NAME_INDEX ? '' : $routeName);

                    if ($fullPath && $fullRouteName) {
                        $route = new Route($fullPath, [
                            '_controller' => $reflectionClass->getName() . '::resolveSimpleRoute',
                            'routeName' => $routeName,
                        ]);

                        $collection->add($fullRouteName, $route);
                    }
                }
            }
        }

        return $collection;
    }

    protected function getName(): string
    {
        return 'simple_routes';
    }
}
