<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Typed\TrimLeft;

class TrimLeftTest extends TestCase
{
    public function testTrimLeft(): void
    {
        // given
        $assert = new TrimLeft();

        // when
        $input = '   hello world   ';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertEquals('hello world   ', $result);
    }
}