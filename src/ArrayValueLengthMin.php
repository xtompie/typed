<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayValueLengthMin implements Assert
{
    public function __construct(
        protected int $min,
        protected ?string $msg = null,
        protected string $key = 'min',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $errors = ErrorCollection::ofEmpty();

        foreach ($input as $index => $value) {
            if (strlen((string) $value) >= $this->min) {
                continue;
            }
            $errors = $errors->addMsg(
                message: $this->msg ?: 'Value must be greather than or equal ' . $this->min,
                key: $this->key,
                space: (string)$index
            );
        }

        return $errors->any() ? $errors : $input;
    }
}
