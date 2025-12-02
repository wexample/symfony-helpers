<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Controller\AbstractController;

class TemplateHelper
{
    public const TEMPLATE_FILE_EXTENSION = '.html.twig';

    protected const string VIEW_PATH_PREFIX = '@';

    public static function removeExtension(string $viewPAthWithExtension): string
    {
        return TextHelper::removeSuffix(
            $viewPAthWithExtension,
            self::TEMPLATE_FILE_EXTENSION
        );
    }

    public static function trimPathPrefix(
        string $domain,
    ): string
    {
        return substr($domain, strlen(self::VIEW_PATH_PREFIX));
    }

    public static function joinNormalizedParts(
        array $parts,
        string $separator = '/'
    ): string
    {
        return implode(
            $separator,
            array_map([TextHelper::class, 'toSnake'], $parts
            )
        );
    }

    public static function explodeControllerNamespaceSubParts(
        string $controllerName,
        string $bundleClassPath = null
    ): array
    {
        $controllerName = AbstractController::removeSuffix($controllerName);
        $parts = explode('\\', $controllerName);

        if ($bundleClassPath) {
            $spliceCount = count(explode('\\', $bundleClassPath));
        } else {
            $spliceCount = 2;
        }

        return array_splice($parts, $spliceCount);
    }
}
