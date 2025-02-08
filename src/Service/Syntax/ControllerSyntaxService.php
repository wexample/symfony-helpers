<?php

namespace Wexample\SymfonyHelpers\Service\Syntax;

use JetBrains\PhpStorm\Pure;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\FileHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

class ControllerSyntaxService extends AbstractSyntaxService
{
    final public const COUSIN_TEST_ROLE_INTEGRATION = 'test_role_integration';

    final public const SUFFIX_TEST = 'Test';

    final public const METHOD_PREFIX_TEST = 'test';

    final public const COUSINS = [
        self::COUSIN_TEST_ROLE_INTEGRATION => [
            VariableHelper::BASE_PATH => null,
            VariableHelper::SUFFIX => self::SUFFIX_TEST,
            VariableHelper::FOLDER => 'tests',
        ],
    ];

    public static function getGroup(): string
    {
        return 'Controller';
    }

    public static function getCousinParameters(string $cousinName): ?array
    {
        return self::COUSINS[$cousinName] ?? null;
    }

    #[Pure]
    public static function buildControllerTestPath(
        string $classPath,
        string $role,
        string $subDir = '',
        string $cousinSuffix = self::SUFFIX_TEST
    ): string {
        $cousinBasePath = ClassHelper::CLASS_TEST_BASE_PATH
            .'Integration\\Role\\'
            .$role
            .($subDir ? ClassHelper::NAMESPACE_SEPARATOR.$subDir : $subDir)
            .'\\Controller\\';

        return ClassHelper::getCousin(
            $classPath,
            ClassHelper::CLASS_ENTITY_BASE_PATH,
            '',
            $cousinBasePath,
            $cousinSuffix
        );
    }

    public function buildControllerPath(string $subDir = ''): string
    {
        return $this->projectDir
            .FileHelper::FOLDER_SEPARATOR
            .ClassHelper::DIR_SRC
            .($subDir ? $subDir.FileHelper::FOLDER_SEPARATOR : '')
            .ClassHelper::CLASS_PATH_PART_CONTROLLER
            .FileHelper::FOLDER_SEPARATOR;
    }
}
