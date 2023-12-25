<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\NotBlank;

class NotBlankTest extends TestCase
{
    public function testValidNotBlank(): void
    {
        // Given
        $assert = new NotBlank();

        // When
        $input = 'non-empty-value';
        $result = $assert->assert($input, 'string');

        // Then
        $this->assertEquals($input, $result);
    }

    public function testInvalidNotBlank(): void
    {
        // Given
        $assert = new NotBlank();

        // When
        $input = '';
        $result = $assert->assert($input, 'string');

        // Then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // Given
        $assert = new NotBlank(msg: 'Custom error message');

        // When
        $input = '';
        $result = $assert->assert($input, 'string');

        // Then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // Given
        $assert = new NotBlank(key: 'custom_key');

        // When
        $input = '';
        $result = $assert->assert($input, 'string');

        // Then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
