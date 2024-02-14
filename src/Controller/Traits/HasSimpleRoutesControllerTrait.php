<?php

namespace Wexample\SymfonyHelpers\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait HasSimpleRoutesControllerTrait
{
    public static function getSimpleRoutes(): array
    {
        return [];
    }

    /**
     * Should implement #[IsSimpleMethodResolver]
     * @param string $routeName
     * @return Response
     */
    abstract public function simpleRoutesResolver(string $routeName): Response;

    public static function getControllerRouteAttribute(): Route
    {
        $reflectionClass = new \ReflectionClass(
            static::class
        );

        $routeAttributes = $reflectionClass->getAttributes(Route::class);

        return $routeAttributes[0]->newInstance();
    }
}
