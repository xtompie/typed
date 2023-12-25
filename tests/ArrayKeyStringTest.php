<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayKeyString;

class ArrayKeyStringTest extends TestCase
{
    public function testValidArrayKeys(): void
    {
        // given
        $assert = new ArrayKeyString();

        // when
        $input = ['key1' => 'value1', 'key2' => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArrayKeys(): void
    {
        // given
        $assert = new ArrayKeyString();

        // when
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new ArrayKeyString(msg: 'Custom error message');

        // when
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new ArrayKeyString(key: 'custom_key');

        // when
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
