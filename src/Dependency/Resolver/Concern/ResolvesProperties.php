<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Concern;

use Container\Core\Container;
use Container\Core\Dependency\Resolver\DependencyResolverException;

trait ResolvesProperties {

    private function resolveProperty(\ReflectionProperty $property): object {
        $property_type = $property->getType();
        if ($property_type === null) {
            throw new DependencyResolverException(
                "Cannot resolve not typed property '\${$property->getName()}'"
            );
        }

        if ($property_type instanceof \ReflectionUnionType) {
            throw new DependencyResolverException(
                "Cannot resolve union property '\${$property->getName()}'"
            );
        }

        if ($property_type instanceof \ReflectionNamedType) {
            if (!$property_type->isBuiltin()) {
                return Container::getInstance()->make($property_type->getName());
            }

            throw new DependencyResolverException(
                "Cannot resolve builtin property '\${$property->getName()}'"
            );
        }

        throw new DependencyResolverException(
            "Cannot resolve property '\${$property->getName()}'"
        );
    }
}