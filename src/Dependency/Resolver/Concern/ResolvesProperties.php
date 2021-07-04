<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Concern;

use Container\Core\Container;
use Container\Core\Dependency\Resolver\Exception\PropertyException;
use Container\Core\Dependency\Resolver\Exception\PropertyNotTypedException;
use Container\Core\Dependency\Resolver\Exception\PropertyWithBuiltinTypeException;
use Container\Core\Dependency\Resolver\Exception\PropertyWithUnionTypeException;

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
                return Container::getInstance()->make($property_type->getName());
            }

            throw new PropertyWithBuiltinTypeException($property);
        }

        throw new PropertyException($property);
    }
}