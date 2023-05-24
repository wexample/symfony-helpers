<?php

namespace Wexample\SymfonyHelpers\Tests\Class;

use Wexample\SymfonyHelpers\Tests\Traits\TestCase\Application\IntegrationTestCaseTrait;

abstract class AbstractApplicationTestCase extends AbstractSymfonyTestCase
{
    use IntegrationTestCaseTrait;
}