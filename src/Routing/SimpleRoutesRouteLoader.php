<?php

namespace Wexample\SymfonyHelpers\Routing;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Wexample\SymfonyHelpers\Controller\Traits\HasSimpleRoutesControllerTrait;
use Wexample\SymfonyHelpers\Routing\Traits\RoutePathBuilderTrait;

class SimpleRoutesRouteLoader extends AbstractRouteLoader
{
    use RoutePathBuilderTrait;
    
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

            if (!empty($routeAttributes) && method_exists($controller, 'getSimpleRoutes')) {
                /** @var HasSimpleRoutesControllerTrait $controller */
                $routes = $controller::getSimpleRoutes();

                foreach ($routes as $routeName) {
                    $fullRouteName = $this->buildRouteNameFromController($controller, $routeName);
                    $fullPath = $this->buildRoutePathFromController($controller, $routeName);

                    if ($fullPath && $fullRouteName) {
                        $route = new Route($fullPath, [
                            '_controller' => $reflectionClass->getName().'::resolveSimpleRoute',
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