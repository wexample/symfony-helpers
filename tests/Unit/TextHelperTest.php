<?php

namespace Wexample\SymfonyApi\Tests\Unit\Helper;

use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyTesting\Tests\AbstractApplicationTestCase;

class TextHelperTest extends AbstractApplicationTestCase
{
    public function testHelper()
    {
        $this->assertEquals(
            'some-thing-in-class-case',
            TextHelper::toKebab('Some_ThingInClassCase')
        );
    }
}
