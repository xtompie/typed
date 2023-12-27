<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayLengthMin;

class ArrayLengthMinTest extends TestCase
{
    public function testValidValue(): void
    {
        // given
        $assert = new ArrayLengthMin(3);

        // when
        $result = $assert->assert([1, 2, 3], 'array');

        // then
        $this->assertEquals([1, 2, 3], $result);
    }

    public function testInvalidValue(): void
    {
        // given
        $assert = new ArrayLengthMin(3);

        // when
        $result = $assert->assert([1, 2], 'array');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new ArrayLengthMin(min: 3, msg: 'Custom error message');

        // when
        $result = $assert->assert([1, 2], 'array');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomErrorKey(): void
    {
        // given
        $assert = new ArrayLengthMin(min: 3, key: 'custom_key');

        // when
        $result = $assert->assert([1, 2], 'array');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}