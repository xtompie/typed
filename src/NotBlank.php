<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class NotBlank implements Assert
{
    public function __construct(
        protected string $msg = 'Value must not be blank',
        protected string $key = 'not_blank',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if (empty($input)) {
            return ErrorCollection::ofErrorMsg(
                message: $this->msg,
                key: $this->key,
            );
        }

        return $input;
    }
}
