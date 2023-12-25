<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayKeyRegex;

class ArrayKeyRegexTest extends TestCase
{
    public function testValidArrayKeys(): void
    {
        // given
        $assert = new ArrayKeyRegex('/^[a-zA-Z]+$/');

        // when
        $input = ['aa' => 'value1', 'bb' => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArrayKeys(): void
    {
        // given
        $assert = new ArrayKeyRegex('/^[a-zA-Z]+$/');

        // when
        $input = ['key1' => 'value1', '123' => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new ArrayKeyRegex(regex: '/^[a-zA-Z]+$/', msg: 'Custom error message');

        // when
        $input = ['key1' => 'value1', '123' => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new ArrayKeyRegex(regex: '/^[a-zA-Z]+$/', key: 'custom_key');

        // when
        $input = ['key1' => 'value1', '123' => 'value2'];
        $result = $assert->assert($input, 'array');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
