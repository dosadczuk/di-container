<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers\Concerns;

use Foundation\Container\Container;
use Foundation\Container\Resolvers\Exceptions\PropertyException;
use Foundation\Container\Resolvers\Exceptions\PropertyNotTypedException;
use Foundation\Container\Resolvers\Exceptions\PropertyWithBuiltinTypeException;
use Foundation\Container\Resolvers\Exceptions\PropertyWithUnionTypeException;

trait ResolvesProperties {

    private function resolveProperty(\ReflectionProperty $property): object {
        $property_type = $property->getType();
        if ($property_type === null) {
            throw new PropertyNotTypedException($property);
        }

        if ($property_type instanceof \ReflectionUnionType) {
            throw new PropertyWithUnionTypeException($property);
        }

        if ($property_type instanceof \ReflectionNamedType) {
            if (!$property_type->isBuiltin()) {
                return Container::get($property_type->getName());
            }

            throw new PropertyWithBuiltinTypeException($property);
        }

        throw new PropertyException($property);
    }
}