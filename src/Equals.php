<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Assert;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Equals implements Assert
{
    public function __construct(
        protected mixed $value,
        protected string $msg = 'Invalid value',
        protected string $key = 'equals',
    ){
    }

    public function assert(mixed $input, string $type): mixed
    {
        if ($input !== $this->value) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg,
                key: $this->key,
            );
        }

        return $input;
    }
}
