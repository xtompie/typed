<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayLengthMin implements Assert
{
    public function __construct(
        protected int $min,
        protected ?string $msg = null,
        protected string $key = 'length_min',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if (count((array) $input) < $this->min) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Number of items must be greather than or equal ' . $this->min,
                key: $this->key,
            );
        }

        return $input;
    }
}
