<?php

namespace Wexample\SymfonyHelpers\Controller;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyHelpers\Traits\BundleClassTrait;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public const PATH_TYPE_SHOW = VariableHelper::SHOW;

    public const ROUTE_OPTION_KEY_EXPOSE = 'expose';

    public const ROUTE_OPTIONS_ONLY_EXPOSE = [self::ROUTE_OPTION_KEY_EXPOSE => true];

    public static function getControllerBundle(): ?string
    {
        $current = static::class;

        if (ClassHelper::classUsesTrait($current, BundleClassTrait::class)) {
            /** @var BundleClassTrait $current */
            return $current::getBundleClassName();
        }

        return null;
    }

    public static function buildRouteName(string $suffix): string
    {
        $reflexion = new ReflectionClass(static::class);

        foreach ($reflexion->getAttributes() as $attribute) {
            if (Route::class === $attribute->getName()) {
                $arguments = $attribute->getArguments();

                if (isset($arguments[VariableHelper::NAME])) {
                    return $arguments[VariableHelper::NAME].$suffix;
                }
            }
        }

        return $suffix;
    }

    public static function getSimpleRoutes(): array
    {
        return [];
    }

    public function simpleRoutesResolver(string $routeName): Response
    {
        return $this->renderPage(
            $routeName,
            [
                'page_name' => $routeName,
            ]
        );
    }

    public static function getControllerRouteAttribute(): Route
    {
        $reflectionClass = new \ReflectionClass(
            static::class
        );

        $routeAttributes = $reflectionClass->getAttributes(Route::class);

        return $routeAttributes[0]->newInstance();
    }
}
