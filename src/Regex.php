<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Regex implements Assert
{
    public function __construct(
        protected string $regex,
        protected ?string $msg = null,
        protected string $key = 'regexp',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if (preg_match($this->regex, (string) $input) !== 1) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Value does not match ' . $this->regex,
                key: $this->key,
            );
        }

        return $input;
    }
}
