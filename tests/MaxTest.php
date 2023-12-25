<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Max;

class MaxTest extends TestCase
{
    public function testValidValue(): void
    {
        // given
        $assert = new Max(10);

        // when
        $result = $assert->assert(5, 'int');

        // then
        $this->assertEquals(5, $result);
    }

    public function testInvalidValue(): void
    {
        // given
        $assert = new Max(10);

        // when
        $result = $assert->assert(15, 'int');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Max(max: 10, msg: 'Custom error message');

        // when
        $result = $assert->assert(15, 'int');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomErrorKey(): void
    {
        // given
        $assert = new Max(max: 10, key: 'custom_key');

        // when
        $result = $assert->assert(15, 'int');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
