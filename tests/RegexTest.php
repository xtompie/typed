<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Regex;

class RegexTest extends TestCase
{
    public function testValidRegex(): void
    {
        // given
        $assert = new Regex('/[0-9]+/');

        // when
        $result = $assert->assert('123', 'string');

        // then
        $this->assertEquals('123', $result);
    }

    public function testInvalidRegex(): void
    {
        // given
        $assert = new Regex('/[0-9]+/');

        // when
        $result = $assert->assert('abc', 'string');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Regex('/[0-9]+/', msg: 'Custom error message');

        // when
        $result = $assert->assert('abc', 'string');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new Regex('/[0-9]+/', key: 'custom_key');

        // when
        $result = $assert->assert('abc', 'string');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}