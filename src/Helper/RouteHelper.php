<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Controller\AbstractController;

class RouteHelper
{
    public static function buildRoutePrefixFromControllerClass(
        AbstractController|string $controllerClass
    ): string {
        return TextHelper::toSnake(
                AbstractController::removeSuffix(
                    ClassHelper::getShortName(
                        $controllerClass
                    )
                )
            ).'_';
    }
}
