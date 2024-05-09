<?php

namespace Wexample\SymfonyHelpers\Routing;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Wexample\SymfonyHelpers\Helper\TextHelper;

class SimpleRoutesRouteLoader extends AbstractRouteLoader
{
    public function __construct(
        protected RewindableGenerator $taggedControllers,
        string $env = null
    ) {
        parent::__construct($env);
    }

    protected function loadOnce(
        $resource,
        string $type = null
    ): RouteCollection {
        $collection = new RouteCollection();

        foreach ($this->taggedControllers as $controller) {
            $reflectionClass = new \ReflectionClass($controller);
            $routeAttributes = $reflectionClass->getAttributes(\Symfony\Component\Routing\Annotation\Route::class);

            if (!empty($routeAttributes)) {
                $routeAttribute = $routeAttributes[0]->newInstance();
                $basePath = $routeAttribute->getPath();
                $baseName = $routeAttribute->getName();

                if (method_exists($controller, 'getSimpleRoutes')) {
                    $routes = $controller::getSimpleRoutes();

                    foreach ($routes as $routeName) {
                        $fullRouteName = $baseName.$routeName;
                        $fullPath = $basePath.TextHelper::toKebab($routeName);

                        $route = new Route($fullPath, [
                            '_controller' => $reflectionClass->getName().'::simpleRoutesResolver',
                            'routeName' => $routeName
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