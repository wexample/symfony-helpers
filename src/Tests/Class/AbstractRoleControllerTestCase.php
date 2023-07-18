<?php

namespace Wexample\SymfonyHelpers\Tests\Class;

use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Service\Syntax\ControllerSyntaxService;
use Wexample\SymfonyHelpers\Tests\Class\Traits\ControllerTestCaseTrait;

abstract class AbstractRoleControllerTestCase extends AbstractRoleTestCase
{
    use ControllerTestCaseTrait;

    public const APPLICATION_ROLE_TEST_CLASS_PATH = '\\App\\Tests\\Application\\Roles\\';

    public static function getControllerClass(): string
    {
        return static::buildControllerClassPath();
    }

    /**
     * Guess controller class name from test controller class name.
     *
     * @param string|null $testControllerClass
     * @return string
     */
    public static function buildControllerClassPath(
        string $testControllerClass = null
    ): string {
        $testControllerClass = $testControllerClass ?: static::class;

        if (!str_starts_with($testControllerClass, ClassHelper::NAMESPACE_SEPARATOR)) {
            $testControllerClass = ClassHelper::NAMESPACE_SEPARATOR.$testControllerClass;
        }

        $testControllerClass = TextHelper::trimString(
            $testControllerClass,
            static::getRoleTestClassBasePath(),
            ControllerSyntaxService::SUFFIX_TEST
        );

        // Count the number of chunks to remove,
        // first separators adds one more chunk which is expected,
        // to remove the RoleName folder.
        $removeChunksLength = count(explode(ClassHelper::NAMESPACE_SEPARATOR, self::APPLICATION_ROLE_TEST_CLASS_PATH));

        $chunks = explode(ClassHelper::NAMESPACE_SEPARATOR, $testControllerClass);
        $chunks = array_splice($chunks, $removeChunksLength);

        array_unshift($chunks, ClassHelper::CLASS_PATH_PART_APP);

        return ClassHelper::join($chunks, true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createGlobalClient();
    }
}
