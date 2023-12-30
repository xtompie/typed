<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Typed\TrimRight;

class TrimRightTest extends TestCase
{
    public function testTrimRight(): void
    {
        // given
        $assert = new TrimRight();

        // when
        $input = '   hello world   ';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertEquals('   hello world', $result);
    }
}