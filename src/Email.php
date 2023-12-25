<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Email extends Regex
{
    public function __construct(
        protected string $regex = '/^.+\@\S+\.\S+$/',
        protected ?string $msg = 'Value must be a valid email address',
        protected string $key = 'email',
    ) {
    }
}
