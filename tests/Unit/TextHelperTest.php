<?php

namespace Wexample\SymfonyHelpers\Tests\Unit\Helper;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wexample\Helpers\Helper\TextHelper;

class TextHelperTest extends WebTestCase
{
    public function testHelper()
    {
        $this->assertEquals(
            'some-thing-in-class-case',
            TextHelper::toKebab('Some_ThingInClassCase')
        );
    }
}
