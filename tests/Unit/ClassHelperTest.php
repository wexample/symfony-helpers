<?php

namespace Wexample\SymfonyHelpers\Tests\Unit\Helper;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wexample\Helpers\Helper\ClassHelper;

class ClassHelperTest extends WebTestCase
{
    public function testHelper()
    {
        $this->assertEquals(
            'This\\Is\\ATest\\ClassPath',
            ClassHelper::buildClassNameFromPath('this/is/a_test/class-path')
        );
    }
}
