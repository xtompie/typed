<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Choice implements Assert
{
    public function __construct(
        protected array $options,
        protected ?string $msg = null,
        protected string $key = 'choice',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        if ($type === 'array') {
            $invalids = array_filter((array)$input, fn ($value) => !in_array($value, $this->options));
            if ($invalids) {
                return ErrorCollection::ofErrorMsg(
                    message: $this->msg ?: 'Values {' . implode(', ', $invalids) . '} must be a one of: {' . implode(', ', $this->options) . '}',
                    key: $this->key,
                );
            }

        } else {
            if (!in_array($input, $this->options)) {
                return ErrorCollection::ofErrorMsg("Value must be a one of " . implode(', ', $this->options), 'choice');
            }
        }


        return $input;
    }
}
