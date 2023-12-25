<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ToString implements Assert
{
    public function assert(mixed $input, string $type): mixed
    {
        return (string)$input;
    }
}
