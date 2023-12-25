<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Alnum extends Regex
{
    public function __construct(
        protected string $regex = '/^[a-zA-Z0-9]+$/',
        protected ?string $msg = 'Only alphanumeric character allowed',
        protected string $key = 'alnum',
    ) {
    }
}
