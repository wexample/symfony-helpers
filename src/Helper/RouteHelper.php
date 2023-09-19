<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\SymfonyHelpers\Controller\AbstractController;

class RouteHelper
{
    public static function buildRoutePrefixFromControllerClass(
        AbstractController|string $controllerClass
    ): string {
        return TextHelper::toSnake(
            TextHelper::trimStringSuffix(
                ClassHelper::getShortName(
                    $controllerClass
                ),
                ClassHelper::CLASS_PATH_PART_CONTROLLER
            )
        ).'_';
    }
}
