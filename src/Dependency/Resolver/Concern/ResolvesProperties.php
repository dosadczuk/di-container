<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Concern;

use Container\Core\Dependency\Resolver\DependencyResolverException;
use function Container\Core\make;

/**
 * @internal
 */
trait ResolvesProperties {

    private function resolveProperty(\ReflectionProperty $property): object {
        if (!$property->hasType()) {
            throw new DependencyResolverException(
                "Cannot resolve not typed property '\${$property->getName()}'"
            );
        }

        $property_type = $property->getType();
        if ($property_type instanceof \ReflectionUnionType) {
            throw new DependencyResolverException(
                "Cannot resolve union typed property '\${$property->getName()}'"
            );
        }

        if ($property_type instanceof \ReflectionNamedType) {
            if ($property_type->isBuiltin()) {
                throw new DependencyResolverException(
                    "Cannot resolve builtin typed property '\${$property->getName()}'"
                );
            }

            return make($property_type->getName());
        }

        throw new DependencyResolverException(
            "Cannot resolve property '\${$property->getName()}'"
        );
    }
}