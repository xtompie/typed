<?php

namespace Xtompie\Typed;

use Attribute;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayOf implements PostAssert
{
    public function __construct(
        protected string $type,
    ) {
    }

    public function assert(mixed $input): mixed
    {
        if (!is_array($input)) {
            return ErrorCollection::ofErrorMsg('Value must be an array', 'array');
        }

        $errors = ErrorCollection::ofEmpty();
        $typed = [];
        foreach ($input as $index => $inputItem) {
            $object = Typed::typed($this->type, $inputItem);
            if ($object instanceof ErrorCollection) {
                $errors = $errors->merge($object->withPrefix("$index."));
            }
            elseif ($errors->none()) {
                $typed[$index] = $object;
            }
        }

        if ($errors->any()) {
            return $errors;
        }

        return $typed;
    }
}
