<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Alpha extends Regex
{
    public function __construct(
        protected string $regex = '/^[a-zA-Z]+$/',
        protected ?string $msg = 'Only alphabetic character allowed',
        protected string $key = 'alpha',
    ) {
    }
}
