<?php

namespace Xtompie\Typed;

interface Assert
{
    public function assert(mixed $input, string $type): mixed;
}