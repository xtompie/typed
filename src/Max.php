<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Max implements Assert
{
    public function __construct(
        protected int $max,
        protected ?string $msg = null,
        protected string $key = 'max',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if ((int)$input > $this->max) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Value should be less than or equal ' . $this->max,
                key: $this->key,
            );
        }

        return $input;
    }
}
