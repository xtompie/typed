<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class TrimLeft implements Assert
{
    public function __construct(
        protected string $characters = " \n\r\t\v\x00",
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        return ltrim($input, $this->characters);
    }
}
