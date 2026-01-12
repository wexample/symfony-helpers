<?php

namespace Wexample\SymfonyHelpers\Controller;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Annotation\Route;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyHelpers\Traits\BundleClassTrait;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public const string PATH_TYPE_SHOW = VariableHelper::SHOW;

    final public const DEFAULT_ROUTE_NAME_INDEX = 'index';
    final public const DEFAULT_ROUTE_NAME_CREATE = 'create';
    final public const DEFAULT_ROUTE_NAME_DELETE = 'delete';
    final public const DEFAULT_ROUTE_NAME_DETAIL = 'detail';
    final public const DEFAULT_ROUTE_NAME_LIST = VariableHelper::LIST;
    final public const DEFAULT_ROUTE_NAME_SHOW = VariableHelper::SHOW;
    final public const DEFAULT_ROUTE_NAME_UPDATE = 'update';

    public const string ROUTE_OPTION_KEY_EXPOSE = 'expose';

    public const array ROUTE_OPTIONS_ONLY_EXPOSE = [self::ROUTE_OPTION_KEY_EXPOSE => true];
    public const array ROUTE_OPTIONS_METHOD_ONLY_HEAD = [Request::METHOD_HEAD];
    public const array ROUTE_OPTIONS_METHOD_ONLY_GET = [Request::METHOD_GET];
    public const array ROUTE_OPTIONS_METHOD_ONLY_POST = [Request::METHOD_POST];
    public const array ROUTE_OPTIONS_METHOD_ONLY_PUT = [Request::METHOD_PUT];
    public const array ROUTE_OPTIONS_METHOD_ONLY_PATCH = [Request::METHOD_PATCH];
    public const array ROUTE_OPTIONS_METHOD_ONLY_DELETE = [Request::METHOD_DELETE];
    public const array ROUTE_OPTIONS_METHOD_ONLY_PURGE = [Request::METHOD_PURGE];
    public const array ROUTE_OPTIONS_METHOD_ONLY_OPTIONS = [Request::METHOD_OPTIONS];
    public const array ROUTE_OPTIONS_METHOD_ONLY_TRACE = [Request::METHOD_TRACE];
    public const array ROUTE_OPTIONS_METHOD_ONLY_CONNECT = [Request::METHOD_CONNECT];

    /**
     * @return Bundle|string
     */
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
    )
    {
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
                    return $arguments[VariableHelper::NAME] . $suffix;
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
