<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Equals;

class EqualsTest extends TestCase
{
    public function testEqualValues(): void
    {
        // given
        $assert = new Equals(5);

        // when
        $result = $assert->assert(5, 'integer');

        // then
        $this->assertEquals(5, $result);
    }

    public function testNotEqualValues(): void
    {
        // given
        $assert = new Equals(5);

        // when
        $result = $assert->assert(10, 'integer');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new Equals(5, 'Custom error message');

        // when
        $result = $assert->assert(10, 'integer');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomErrorKey(): void
    {
        // given
        $assert = new Equals(5, key: 'custom_key');

        // when
        $result = $assert->assert(10, 'integer');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
