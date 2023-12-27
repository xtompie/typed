<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayOf;

class ArrayOfTest extends TestCase
{
    public function testValidArray(): void
    {
        // given
        $assert = new ArrayOf('string');

        // when
        $input = ['hello', 'world'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArray(): void
    {
        // given
        $assert = new ArrayOf('int');

        // when
        $input = ['hello', 'world'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }
}