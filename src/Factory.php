<?php

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Factory
{
    public function __construct(
        protected string $class,
        protected string $method = 'typed',
    ) {
    }

    public function class(): string
    {
        return $this->class;
    }

    public function method(): string
    {
        return $this->method;
    }
}
