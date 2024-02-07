<?php

namespace Wexample\SymfonyHelpers\Controller;

use ReflectionClass;
use Symfony\Component\Routing\Annotation\Route;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyHelpers\Traits\BundleClassTrait;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public const PATH_TYPE_SHOW = VariableHelper::SHOW;

    public const ROUTE_OPTION_KEY_EXPOSE = 'expose';

    public const ROUTE_OPTIONS_ONLY_EXPOSE = [self::ROUTE_OPTION_KEY_EXPOSE => true];

    protected function getControllerBundle(): ?string
    {
        if (ClassHelper::classUsesTrait($this, BundleClassTrait::class)) {
            /** @var BundleClassTrait $this */
            return $this->getBundleClassName();
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
}
