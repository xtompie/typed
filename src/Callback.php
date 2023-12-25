<?php

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Callback implements Closure
{
    public function __construct(
        protected string $method = 'typed',
    ) {
    }

    public function assert(mixed $input): mixed
    {
        return call_user_func([$input, $this->method]);
    }
}
