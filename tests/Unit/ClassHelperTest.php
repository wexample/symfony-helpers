<?php

namespace Wexample\SymfonyHelpers\Tests\Unit\Helper;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyTesting\Tests\AbstractApplicationTestCase;

class ClassHelperTest extends AbstractApplicationTestCase
{
    public function testHelper()
    {
        $this->assertEquals(
            'This\\Is\\ATest\\ClassPath',
            ClassHelper::buildClassNameFromPath('this/is/a_test/class-path')
        );
    }
}
