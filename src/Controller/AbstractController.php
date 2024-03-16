<?php

namespace Wexample\SymfonyHelpers\Controller;

use ReflectionClass;
use Symfony\Component\Routing\Annotation\Route;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
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

    protected function getParameterOrDefault(
        string $name,
        array|bool|float|int|null|string $default
    ) {
        if (!$this->container->get('parameter_bag')->has($name)) {
            return $default;
        }

        return $this->getParameter($name);
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

    public static function removeSuffix(string $className): string
    {
        return TextHelper::removeSuffix(
            $className,
            ClassHelper::CLASS_PATH_PART_CONTROLLER
        );
    }
}
