<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayKeyString implements Assert
{
    public function __construct(
        protected string $msg = 'Array key must be of string type',
        protected string $key = 'array_key_string',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $errors = ErrorCollection::ofEmpty();

        foreach ($input as $index => $value) {
            if (is_string($index)) {
                continue;
            }
            $errors = $errors->addMsg(
                message: $this->msg,
                key: $this->key,
                space: (string)$index
            );
        }

        return $errors->any() ? $errors : $input;
    }
}
