<?php

namespace Xtompie\Typed;

use Attribute;
use ReflectionClass;
use ReflectionParameter;
use Xtompie\Result\Error;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_CLASS)]
class Only implements Assert
{
    public function assert(mixed $primitive, string $type): mixed
    {
        $class = new ReflectionClass($type);

        if (!is_array($primitive)) {
            return ErrorCollection::ofErrorMsg('Value must be an assoc array', 'array');
        }

        $target = array_map(
            fn (ReflectionParameter $parameter) => Typed::objectParameterSource($parameter),
            $class->getConstructor()->getParameters()
        );
        $input = array_keys($primitive);
        $invalid = array_values(array_filter($input, fn ($key) => !in_array($key, $target)));

        if ($invalid) {
            return ErrorCollection::ofErrors(array_map(
                fn ($key) => Error::of('Invalid key: ' . $key, 'only'),
                $invalid
            ));
        }

        return $primitive;
    }
}
