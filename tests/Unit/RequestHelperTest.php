<?php

namespace Wexample\SymfonyHelpers\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyHelpers\Helper\RequestHelper;
use Wexample\SymfonyHelpers\Helper\TypesHelper;

class RequestHelperTest extends TestCase
{
    public function testAValueAskedAsTextStaysText(): void
    {
        $this->assertSame('411', RequestHelper::parseRequestValue('411', TypesHelper::STRING));
        $this->assertSame('007', RequestHelper::parseRequestValue('007', TypesHelper::STRING));
        $this->assertSame('true', RequestHelper::parseRequestValue('true', TypesHelper::STRING));
    }

    public function testOtherValuesAreRead(): void
    {
        $this->assertSame(411, RequestHelper::parseRequestValue('411', TypesHelper::INTEGER));
        $this->assertSame(1.5, RequestHelper::parseRequestValue('1.5'));
        $this->assertTrue(RequestHelper::parseRequestValue('1', TypesHelper::BOOLEAN));
    }
}
