<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Alnum;

class AlnumTest extends TestCase
{
    public function testValidAlnum(): void
    {
        // given
        $assert = new Alnum();

        // when
        $result = $assert->assert('abc123', 'string');

        // then
        $this->assertEquals('abc123', $result);
    }

    public function testInvalidAlnum(): void
    {
        // given
        $assert = new Alnum();

        // when
        $result = $assert->assert('abc@123', 'string');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Alnum(msg: 'Custom error message');

        // when
        $result = $assert->assert('abc@123', 'string');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new Alnum(key: 'custom_key');

        // when
        $result = $assert->assert('abc@123', 'string');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
