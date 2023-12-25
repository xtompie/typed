<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Alpha;

class AlphaTest extends TestCase
{
    public function testValidAlpha(): void
    {
        // given
        $assert = new Alpha();

        // when
        $result = $assert->assert('abc', 'string');

        // then
        $this->assertEquals('abc', $result);
    }

    public function testInvalidAlpha(): void
    {
        // given
        $assert = new Alpha();

        // when
        $result = $assert->assert('abc123', 'string');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Alpha(msg: 'Custom error message');

        // when
        $result = $assert->assert('abc123', 'string');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new Alpha(key: 'custom_key');

        // when
        $result = $assert->assert('abc123', 'string');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
