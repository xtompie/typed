<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Digit extends Regex
{
    public function __construct(
        protected string $regex = '/^[0-9]+$/',
        protected ?string $msg = 'Only numeric character allowed',
        protected string $key = 'digit',
    ) {
    }
}
