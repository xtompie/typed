<?php

namespace Xtompie\Typed;

use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Xtompie\Result\ErrorCollection;

final class Typed
{
    /**
     * @param string $class
     * @param mixed $input
     * @return object
     */
    public static function typed(string $type, mixed $input): mixed
    {
        return match($type) {
            'null' =>  is_null($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type null', 'null'),
            'boolean' =>  is_bool($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type boolean', 'boolean'),
            'int' =>  is_int($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type int', 'int'),
            'float' =>  is_float($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type float', 'float'),
            'string' =>  is_string($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type string', 'string'),
            'object' =>  is_object($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type object', 'object'),
            'array' =>  is_array($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type array', 'array'),
            'resource' =>  is_resource($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type resource', 'resource'),
            'mixed' => $input,
            default => static::object(type: $type, input: $input),
        };
    }

    /**
     * @template T of object
     * @param class-string $type
     * @param mixed $input
     * @return T|ErrorCollection
     */
    public static function object(string $type, mixed $input): object
    {
        $class = new ReflectionClass($type);

        $input = static::objectInput($input);
        if ($input instanceof ErrorCollection) {
            return $input;
        }

        $input = static::objectPreAssert($type, $input, $class);
        if ($input instanceof ErrorCollection) {
            return $input;
        }

        $object = static::objectParameters(class: $class, input: $input);
        if ($object instanceof ErrorCollection) {
            return $object;
        }

        $object = static::objectPostAssert(object: $object, class: $class);
        if ($object instanceof ErrorCollection) {
            return $object;
        }

        return $object;
    }

    protected static function objectInput(mixed $input): array|ErrorCollection
    {
        if (!is_array($input) && !is_object($input)) {
            return ErrorCollection::ofErrorMsg('Value must be an array assoc or object', 'object');
        }

        return is_object($input) ? (array)$input : $input;
    }

    protected static function objectPreAssert(string $type, array $input, ReflectionClass $class): array|ErrorCollection
    {
        foreach ($class->getAttributes(PreAssert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof PreAssert) {
                continue;
            }
            $input = $assert->assert($input, $type);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }
        return $input;
    }

    protected static function objectPostAssert(object $object, ReflectionClass $class): mixed
    {
        foreach ($class->getAttributes(PostAssert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof PostAssert) {
                continue;
            }
            $object = $assert->assert($object);
            if ($object instanceof ErrorCollection) {
                return $object;
            }
        }
        return $object;
    }

    protected static function objectParamterPreAssert(string $type, mixed $input, ReflectionParameter $parameter): mixed
    {
        foreach ($parameter->getAttributes(PreAssert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof PreAssert) {
                continue;
            }
            $input = $assert->assert($input, $type);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }
        return $input;
    }

    protected static function objectParamterPostAssert(mixed $input, ReflectionParameter $parameter): mixed
    {
        foreach ($parameter->getAttributes(PostAssert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof PostAssert) {
                continue;
            }
            $input = $assert->assert($input);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }
        return $input;
    }

    protected static function objectParamterType(ReflectionClass $class, ReflectionParameter $parameter): array
    {
        $type = $parameter->getType();
        if (!$type) {
            return [true, 'mixed'];
        }

        if (!$type instanceof ReflectionNamedType) {
            throw new Exception("Unsupported type of parameter {$parameter->name} in class {$class->name}. Only ReflectionNamedType currently is supported.");
        }

        return [
            $type->allowsNull(),
            $type->getName(),
        ];
    }

    protected static function objectParameter(ReflectionClass $class, ReflectionParameter $parameter, mixed $value): mixed
    {
        [$optional, $type] = static::objectParamterType(class: $class, parameter: $parameter);

        if ($value === null && $optional) {
            return $value;
        }

        $value = static::typed($type, $value);
        if ($value instanceof ErrorCollection) {
            return $value;
        }

        $value = static::objectParamterPreAssert(type: $type, input: $value, parameter: $parameter);
        if ($value instanceof ErrorCollection) {
            return $value;
        }

        return static::objectParamterPostAssert(input: $value, parameter: $parameter);
    }

    protected static function objectParameters(ReflectionClass $class, array $input): object
    {
        $args = [];
        $errors = ErrorCollection::ofEmpty();

        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $value = static::objectParameter(
                class: $class,
                parameter: $parameter,
                value: $input[$parameter->name] ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null),
            );
            if ($value instanceof ErrorCollection) {
                $errors = $errors->merge($value->withPrefix($parameter->name . '.'));
            }
            if ($errors->none()) {
                $args[$parameter->name] = $value;
            }
        }

        if ($errors->any()) {
            return $errors;
        }

        return $class->newInstanceArgs($args);
    }
}
