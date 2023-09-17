<?php

namespace Wexample\SymfonyHelpers\Helper;

use App\Wex\BaseBundle\Controller\AbstractController;

class RouteHelper
{
    public static function buildRoutePrefixFromControllerClass(
        AbstractController|string $controllerClass
    ): string {
        return TextHelper::toSnake(
            TextHelper::trimStringSuffix(
                \Wexample\SymfonyHelpers\Helper\ClassHelper::getShortName(
                    $controllerClass
                ),
                \Wexample\SymfonyHelpers\Helper\ClassHelper::CLASS_PATH_PART_CONTROLLER
            )
        ).'_';
    }
}
