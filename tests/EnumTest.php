<?php

use PHPUnit\Framework\TestCase;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Typed;

enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

enum Level: int
{
    case ONE = 1;
    case TWO = 2;
}

class EnumTest extends TestCase
{
    public function testValidEnumString(): void
    {
        // given
        $type = Status::class;

        // when
        $input = 'active';
        $result = Typed::typed($type, $input);

        // then
        $this->assertInstanceOf(Status::class, $result);
        $this->assertEquals(Status::ACTIVE, $result);
    }

    public function testInvalidEnumString(): void
    {
        // given
        $type = Status::class;

        // when
        $input = 'unknown';
        $result = Typed::typed($type, $input);

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
        $this->assertEquals('Value must be a one of active, inactive', $result->first()->message());
    }

    public function testValidEnumInt(): void
    {
        // given
        $type = Level::class;

        // when
        $input = 1;
        $result = Typed::typed($type, $input);

        // then
        $this->assertInstanceOf(Level::class, $result);
        $this->assertEquals(Level::ONE, $result);
    }

    public function testInvalidEnumInt(): void
    {
        // given
        $type = Level::class;

        // when
        $input = 3;
        $result = Typed::typed($type, $input);

        // then
        $this->assertInstanceOf(ErrorCollection::class, $result);
        $this->assertEquals('Value must be a one of 1, 2', $result->first()->message());
    }
}

