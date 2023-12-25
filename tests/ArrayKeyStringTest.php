<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\ArrayKeyString;

class ArrayKeyStringTest extends TestCase
{
    public function testValidArrayKeys(): void
    {
        // Given
        $assert = new ArrayKeyString();

        // When
        $input = ['key1' => 'value1', 'key2' => 'value2'];
        $result = $assert->assert($input, 'array');

        // Then
        $this->assertEquals($input, $result);
    }

    public function testInvalidArrayKeys(): void
    {
        // Given
        $assert = new ArrayKeyString();

        // When
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // Then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // Given
        $assert = new ArrayKeyString(msg: 'Custom error message');

        // When
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // Then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // Given
        $assert = new ArrayKeyString(key: 'custom_key');

        // When
        $input = ['key1' => 'value1', 123 => 'value2'];
        $result = $assert->assert($input, 'array');

        // Then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
