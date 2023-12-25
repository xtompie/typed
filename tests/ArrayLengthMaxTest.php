<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayLengthMax;

class ArrayLengthMaxTest extends TestCase
{
    public function testValidArrayLength(): void
    {
        // given
        $arrayLengthMaxValidator = new ArrayLengthMax(3);

        // when
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArrayLength(): void
    {
        // given
        $arrayLengthMaxValidator = new ArrayLengthMax(2);

        // when
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $arrayLengthMaxValidator = new ArrayLengthMax(max: 2, msg: 'Custom error message');

        // when
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $arrayLengthMaxValidator = new ArrayLengthMax(max: 2, key: 'custom_key');

        // when
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
