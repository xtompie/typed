<?php

namespace Xtompie\Typed;

use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionEnum;
use Xtompie\Result\ErrorCollection;

final class Typed
{
    /**
     * @param string $class
     * @param mixed $input
     * @return object
     */
    public static function typed(string $type, mixed $input, mixed $context = null): mixed
    {
        return match($type) {
            'null' =>  is_null($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type null', 'null'),
            'boolean' =>  is_bool($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type boolean', 'boolean'),
            'bool' =>  is_bool($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type boolean', 'bool'),
            'int' =>  is_int($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type int', 'int'),
            'float' =>  is_float($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type float', 'float'),
            'string' =>  is_string($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type string', 'string'),
            'object' =>  is_object($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type object', 'object'),
            'array' =>  is_array($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type array', 'array'),
            'resource' =>  is_resource($input) ? $input : ErrorCollection::ofErrorMsg('Value must be of type resource', 'resource'),
            'mixed' => $input,
            default => static::object(type: $type, input: $input, context: $context),
        };
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @param mixed $input
     * @return T|ErrorCollection
     */
    public static function object(string $type, mixed $input, mixed $context = null): object
    {
        if (enum_exists($type)) {
            return static::enum($type, $input);
        }

        $class = new ReflectionClass($type);

        $factory = static::objectFactory($class);
        $collection = static::objectCollection($class);

        if (!$factory && !$collection) {
            $input = static::objectInput($input);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }

        $input = static::objectAssert($type, $input, $class, $context);
        if ($input instanceof ErrorCollection) {
            return $input;
        }

        if ($factory) {
            $object = call_user_func([$factory->class() ?: $type, $factory->method()], $input, $context);
        } elseif ($collection) {
            $result = (new ArrayOf($collection->of()))->assert($input);
            $object = $result instanceof ErrorCollection ? $result : $class->newInstance($result);
        } else {
            $object = static::objectParameters(class: $class, input: (array)$input, context: $context);
        }

        if ($object instanceof ErrorCollection) {
            return $object;
        }

        $object = static::objectClosure(object: $object, class: $class, context: $context);
        if ($object instanceof ErrorCollection) {
            return $object;
        }

        return $object;
    }

    protected static function objectFactory(ReflectionClass $class): ?Factory
    {
        foreach ($class->getAttributes(Factory::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    protected static function objectCollection(ReflectionClass $class): ?Collection
    {
        foreach ($class->getAttributes(Collection::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    protected static function objectInput(mixed $input): array|ErrorCollection
    {
        if (!is_array($input) && !is_object($input)) {
            return ErrorCollection::ofErrorMsg('Value must be an array assoc or object', 'object');
        }

        return is_object($input) ? (array)$input : $input;
    }

    protected static function objectAssert(string $type, mixed $input, ReflectionClass $class, mixed $context): mixed
    {
        foreach ($class->getAttributes(Assert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof Assert) {
                continue;
            }
            if ($assert instanceof Contextable) {
                $assert->context($context);
            }
            $input = $assert->assert($input, $type);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }
        return $input;
    }

    protected static function objectClosure(object $object, ReflectionClass $class, mixed $context): mixed
    {
        foreach ($class->getAttributes(Closure::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof Closure) {
                continue;
            }
            if ($assert instanceof Contextable) {
                $assert->context($context);
            }
            $object = $assert->assert($object);
            if ($object instanceof ErrorCollection) {
                return $object;
            }
        }
        return $object;
    }

    protected static function objectParamterAssert(string $type, mixed $input, ReflectionParameter $parameter, mixed $context): mixed
    {
        foreach ($parameter->getAttributes(Assert::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof Assert) {
                continue;
            }
            if ($assert instanceof Contextable) {
                $assert->context($context);
            }
            $input = $assert->assert($input, $type);
            if ($input instanceof ErrorCollection) {
                return $input;
            }
        }
        return $input;
    }

    protected static function objectParamterClosure(mixed $input, ReflectionParameter $parameter, mixed $context): mixed
    {
        foreach ($parameter->getAttributes(Closure::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $assert = $attribute->newInstance();
            if (!$assert instanceof Closure) {
                continue;
            }
            if ($assert instanceof Contextable) {
                $assert->context($context);
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

    protected static function objectParameterValue(ReflectionClass $class, ReflectionParameter $parameter, mixed $value, mixed $context): mixed
    {
        [$optional, $type] = static::objectParamterType(class: $class, parameter: $parameter);

        if ($value === null) {
            if ($optional) {
                return $value;
            }
            else {
                return ErrorCollection::ofErrorMsg('Value is required', 'required');
            }
        }

        $value = static::typed($type, $value);
        if ($value instanceof ErrorCollection) {
            return $value;
        }

        $value = static::objectParamterAssert(type: $type, input: $value, parameter: $parameter, context: $context);
        if ($value instanceof ErrorCollection) {
            return $value;
        }

        return static::objectParamterClosure(input: $value, parameter: $parameter, context: $context);
    }

    public static function objectParameterSource(ReflectionParameter $parameter): string
    {
        foreach ($parameter->getAttributes(Source::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance()->source();
        }

        return $parameter->name;
    }

    protected static function objectParameters(ReflectionClass $class, array $input, mixed $context): object
    {
        $args = [];
        $errors = ErrorCollection::ofEmpty();

        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $source = static::objectParameterSource($parameter);
            $value = static::objectParameterValue(
                class: $class,
                parameter: $parameter,
                value: $input[$source] ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null),
                context: $context
            );
            if ($value instanceof ErrorCollection) {
                $errors = $errors->merge($value->withPrefix($source, '.'));
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

    protected static function enum(string $type, mixed $input): mixed
    {
        $enum = new ReflectionEnum($type);
        $backingType = $enum->getBackingType()?->getName();
        $options = array_map(fn($case) => $case->getBackingValue(), $enum->getCases());

        if ($backingType === 'string') {
            if (!is_string($input)) {
                return ErrorCollection::ofErrorMsg("Value must be of type string", 'enum');
            }
            $enumValue = $type::tryFrom($input);
            if ($enumValue === null) {
                return ErrorCollection::ofErrorMsg("Value must be a one of " . implode(', ', $options), 'enum');
            }
            return $enumValue;
        }

        if ($backingType === 'int') {
            if (!is_int($input)) {
                return ErrorCollection::ofErrorMsg("Value must be of type int", 'enum');
            }
            $enumValue = $type::tryFrom($input);
            if ($enumValue === null) {
                return ErrorCollection::ofErrorMsg("Value must be a one of " . implode(', ', $options), 'enum');
            }
            return $enumValue;
        }

        throw new Exception("Unsupported enum type for {$type}. Only backed enums of type string or int are supported.");
    }
}
