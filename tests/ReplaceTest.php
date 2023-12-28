<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Replace;

class ReplaceTest extends TestCase
{
    public function testValidReplace(): void
    {
        // given
        $assert = new Replace(['foo' => 'bar']);

        // when
        $result = $assert->assert('Hello foo', 'string');

        // then
        $this->assertEquals('Hello bar', $result);
    }

    public function testInvalidReplace(): void
    {
        // given
        $assert = new Replace(['foo' => 'bar']);

        // when
        $result = $assert->assert('Hello world', 'string');

        // then
        $this->assertEquals('Hello world', $result);
    }
}