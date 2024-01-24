<?php

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Collection
{
    public function __construct(
        protected string $of,
    ) {
    }

    public function of(): string
    {
        return $this->of;
    }
}
