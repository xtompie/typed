<?php

declare(strict_types=1);

namespace Xtompie\Typed;

interface Contextable
{
    public function context(mixed $context): void;
}