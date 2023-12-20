<?php

namespace Xtompie\Typed;

interface PreAssert
{
    public function assert(mixed $input, string $type): mixed;
}