<?php

namespace Xtompie\Typed;

interface Closure
{
    public function assert(mixed $input): mixed;
}