<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class LengthMax implements Assert
{
    public function __construct(
        protected int $max,
        protected ?string $msg = null,
        protected string $key = 'length_max',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if (strlen((string) $input) > $this->max) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: 'Length must be less than or equal ' . $this->max,
                key: $this->key,
            );
        }

        return $input;
    }
}
