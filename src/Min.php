<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Min implements Assert
{
    public function __construct(
        protected int $min,
        protected ?string $msg = null,
        protected string $key = 'min',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if ((int)$input < $this->min) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Value should be greather than or equal ' . $this->min,
                key: $this->key,
            );
        }

        return $input;
    }
}
