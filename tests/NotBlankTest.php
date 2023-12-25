<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\NotBlank;

class NotBlankTest extends TestCase
{
    public function testValidNotBlank(): void
    {
        // given
        $assert = new NotBlank();

        // when
        $input = 'non-empty-value';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertEquals($input, $result);
    }

    public function testInvalidNotBlank(): void
    {
        // given
        $assert = new NotBlank();

        // when
        $input = '';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
    }

    public function testCustomErrorMessage(): void
    {
        // given
        $assert = new NotBlank(msg: 'Custom error message');

        // when
        $input = '';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertEquals('Custom error message', $result->first()->message());
    }

    public function testCustomKey(): void
    {
        // given
        $assert = new NotBlank(key: 'custom_key');

        // when
        $input = '';
        $result = $assert->assert($input, 'string');

        // then
        $this->assertEquals('custom_key', $result->first()->key());
    }
}
