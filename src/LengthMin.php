<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class LengthMin implements Assert
{
    public function __construct(
        protected int $min,
        protected ?string $msg = null,
        protected string $key = 'length_min',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if (strlen((string) $input) < $this->min) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Length must be greather than or equal ' . $this->min,
                key: $this->key,
            );
        }

        return $input;
    }
}
