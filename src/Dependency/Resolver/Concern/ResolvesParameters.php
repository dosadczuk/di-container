<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Concern;

use Container\Core\Dependency\Resolver\DependencyResolverException;
use function Container\Core\make;

trait ResolvesParameters {

    /**
     * @param \ReflectionParameter[] $parameters
     * @param array<string, object> $defaults
     *
     * @return object[]
     */
    private function resolveParameters(array $parameters, array $defaults = []): array {
        return array_map(
            function (\ReflectionParameter $parameter) use ($defaults) {
                if (isset($defaults[$parameter->getName()])) {
                    return $defaults[$parameter->getName()];
                }

                return $this->resolveParameter($parameter);
            },
            $parameters
        );
    }

    private function resolveParameter(\ReflectionParameter $parameter): object {
        if (!$parameter->hasType()) {
            throw new DependencyResolverException(
                "Cannot resolve not typed parameter '\${$parameter->getName()}'"
            );
        }

        $parameter_type = $parameter->getType();
        if ($parameter_type instanceof \ReflectionUnionType) {
            throw new DependencyResolverException(
                "Cannot resolve union typed parameter '\${$parameter->getName()}'"
            );
        }

        if ($parameter_type instanceof \ReflectionNamedType) {
            if ($parameter_type->isBuiltin()) {
                throw new DependencyResolverException(
                    "Cannot resolve builtin typed parameter '\${$parameter->getName()}'"
                );
            }

            return make($parameter_type->getName());
        }

        throw new DependencyResolverException(
            "Cannot resolve parameter '\${$parameter->getName()}'"
        );
    }
}