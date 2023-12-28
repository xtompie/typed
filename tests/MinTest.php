<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Min;

class MinTest extends TestCase
{
    public function testValidMin(): void
    {
        // given
        $assert = new Min(5);

        // when
        $result = $assert->assert(10, 'int');

        // then
        $this->assertEquals(10, $result);
    }

    public function testInvalidMin(): void
    {
        // given
        $assert = new Min(5);

        // when
        $result = $assert->assert(3, 'int');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Min(5, msg: 'Custom error message');

        // when
        $result = $assert->assert(3, 'int');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new Min(5, key: 'custom_key');

        // when
        $result = $assert->assert(3, 'int');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}