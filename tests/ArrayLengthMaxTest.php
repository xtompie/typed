<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayLengthMax;

class ArrayLengthMaxTest extends TestCase
{
    public function testValidArrayLength(): void
    {
        // Given
        $arrayLengthMaxValidator = new ArrayLengthMax(3);

        // When
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // Then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArrayLength(): void
    {
        // Given
        $arrayLengthMaxValidator = new ArrayLengthMax(2);

        // When
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // Then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // Given
        $arrayLengthMaxValidator = new ArrayLengthMax(max: 2, msg: 'Custom error message');

        // When
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // Then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // Given
        $arrayLengthMaxValidator = new ArrayLengthMax(max: 2, key: 'custom_key');

        // When
        $input = [1, 2, 3];
        $result = $arrayLengthMaxValidator->assert($input, 'array');

        // Then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
