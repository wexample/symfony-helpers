<?php

namespace Wexample\SymfonyHelpers\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyHelpers\Attribute\SimpleMethodResolver;

trait HasSimpleRoutesControllerTrait
{
    public static function getSimpleRoutes(): array
    {
        return [];
    }

    #[SimpleMethodResolver]
    public function resolveSimpleRoute(string $routeName): Response
    {
        return $this->renderPage(
            $routeName,
        );
    }

    public static function getControllerRouteAttribute(): ?Route
    {
        $reflectionClass = new \ReflectionClass(
            static::class
        );

        $routeAttributes = $reflectionClass->getAttributes(Route::class);

        if (empty($routeAttributes)) {
            return null;
        }

        return $routeAttributes[0]->newInstance();
    }
}
