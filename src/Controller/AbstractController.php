<?php

namespace Wexample\SymfonyHelpers\Controller;

use ReflectionClass;
use Symfony\Component\Routing\Annotation\Route;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
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
