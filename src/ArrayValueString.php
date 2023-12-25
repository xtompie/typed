<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayValueString implements Assert
{
    public function __construct(
        protected string $msg = 'Array value must be of string type',
        protected string $key = 'string',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $errors = ErrorCollection::ofEmpty();

        foreach ($input as $index => $value) {
            if (!is_string($value)) {
                $errors = $errors->addMsg(
                    message: $this->msg,
                    key: $this->key,
                    space: (string)$index
                );
            }
        }

        if ($errors->any()) {
            return $errors;
        }

        return $errors->any() ? $errors : $input;
    }
}
