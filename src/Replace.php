<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Replace implements Assert
{
    public function __construct(
        protected array $replace,
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        return str_replace(array_keys($this->replace), array_values($this->replace), $input);
    }
}
