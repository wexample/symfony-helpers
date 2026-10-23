<?php

namespace Wexample\SymfonyHelpers\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyHelpers\Class\ArrayToTextTable;

class ArrayToTextTableTest extends TestCase
{
    public function testNamedKeysAreDrawnAsAHeader(): void
    {
        $this->assertSame(
            '┌──────┬─────┐'.PHP_EOL
            .'│ NAME │ QTY │'.PHP_EOL
            .'├──────┼─────┤'.PHP_EOL
            .'│ café │ 12  │'.PHP_EOL
            .'└──────┴─────┘'.PHP_EOL,
            (string) new ArrayToTextTable([['name' => 'café', 'qty' => '12']])
        );
    }

    public function testWideCharactersAndLineBreaksKeepColumnsAligned(): void
    {
        $this->assertSame(
            '┌──────┬───┐'.PHP_EOL
            .'│ 日本 │ 3 │'.PHP_EOL
            .'│      │ 4 │'.PHP_EOL
            .'└──────┴───┘'.PHP_EOL,
            (string) new ArrayToTextTable([['日本', "3\n4"]])
        );
    }
}
