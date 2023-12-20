<?php

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Compose implements PostAssert
{
    public function __construct(
        protected string $method = 'compose',
    ) {
    }

    public function assert(mixed $input): mixed
    {
        return call_user_func([$input, $this->method]);
    }
}
