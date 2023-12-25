<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayValueLengthMax implements Assert
{
    public function __construct(
        protected int $max,
        protected ?string $msg = null,
        protected string $key = 'max',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $errors = ErrorCollection::ofEmpty();

        foreach ($input as $index => $value) {
            if (strlen((string) $value) <= $this->max) {
                continue;
            }
            $errors = $errors->addMsg(
                message: $this->msg ?: 'Value must be less than or equal ' . $this->max,
                key: $this->key,
                space: (string)$index
            );
        }

        return $errors->any() ? $errors : $input;
    }
}
