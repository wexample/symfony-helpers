<?php

namespace Wexample\SymfonyHelpers\Service\Syntax;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

class EntitySyntaxService extends AbstractSyntaxService
{
    final public const COUSIN_API = 'api';

    final public const COUSIN_TEST = 'test';

    final public const COUSIN_TRAIT_MANIPULATOR = 'trait_manipulator';

    final public const COUSINS = [
        self::COUSIN_API => [
            VariableHelper::BASE_PATH => ClassHelper::CLASS_API_BASE_PATH
                .ClassHelper::NAMESPACE_SEPARATOR
                .ClassHelper::CLASS_PATH_PART_CONTROLLER
                .ClassHelper::NAMESPACE_SEPARATOR
                .ClassHelper::CLASS_PATH_PART_ENTITY
                .ClassHelper::NAMESPACE_SEPARATOR,
            VariableHelper::SUFFIX => 'Controller',
        ],
        self::COUSIN_TRAIT_MANIPULATOR => [
            VariableHelper::BASE_PATH => ClassHelper::CLASS_ENTITY_BASE_PATH.'Traits\\Manipulator\\',
            VariableHelper::SUFFIX => 'EntityManipulatorTrait',
        ],
        self::COUSIN_TEST => [
            VariableHelper::BASE_PATH => ClassHelper::CLASS_TEST_BASE_PATH.'Unit\\Entity\\',
            VariableHelper::SUFFIX => 'EntityTest',
            VariableHelper::FOLDER => 'tests',
        ],
    ];

    public static function getGroup(): string
    {
        return 'Entity';
    }

    public static function getCousinParameters(string $cousinName): ?array
    {
        return self::COUSINS[$cousinName] ?? null;
    }
}
