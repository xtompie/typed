<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Source
{
    public function __construct(
        protected string $source,
    ) {
    }

    public function source(): string
    {
        return $this->source;
    }
}
