<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayKeyRegex implements Assert
{
    public function __construct(
        protected string $regex,
        protected ?string $msg = null,
        protected string $key = 'regexp',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $errors = ErrorCollection::ofEmpty();

        foreach ($input as $key => $value) {
            if (preg_match($this->regex, (string)$key) === 1) {
                continue;
            }
            $errors = $errors->addMsg(
                message: $this->msg ?: 'Value does not match ' . $this->regex,
                key: $this->key,
                space: (string)$key
            );
        }

        return $errors->any() ? $errors : $input;
    }
}
