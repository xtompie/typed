<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Trim;

class TrimTest extends TestCase
{
    public function testValidTrim(): void
    {
        // given
        $assert = new Trim();

        // when
        $result = $assert->assert('  Hello World  ', 'string');

        // then
        $this->assertEquals('Hello World', $result);
    }
}