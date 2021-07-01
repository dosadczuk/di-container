<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers\Concerns;

use Foundation\Container\Container;
use Foundation\Container\Resolvers\Exceptions\ParameterException;
use Foundation\Container\Resolvers\Exceptions\ParameterNotTypedException;
use Foundation\Container\Resolvers\Exceptions\ParameterWithUnionTypeException;

trait ResolvesParameters {

    /**
     * @param \ReflectionParameter[] $dependency_parameters
     *
     * @throws \ReflectionException
     */
    private function resolveParameters(array $dependency_parameters, array $defaults = []): array {
        return array_map(
            function (\ReflectionParameter $parameter) use ($defaults) {
                if (isset($defaults[$parameter->getName()])) {
                    return $defaults[$parameter->getName()];
                }

                return $this->resolveParameter($parameter);
            },
            $dependency_parameters
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function resolveParameter(\ReflectionParameter $parameter): mixed {
        $parameter_type = $parameter->getType();
        if ($parameter_type === null) {
            throw new ParameterNotTypedException($parameter);
        }

        if ($parameter_type instanceof \ReflectionUnionType) {
            throw new ParameterWithUnionTypeException($parameter);
        }

        if ($parameter_type instanceof \ReflectionNamedType) {
            if ($parameter_type->isBuiltin()) {
                return $parameter->getDefaultValue();
            }

            return Container::get($parameter_type->getName());
        }

        throw new ParameterException($parameter);
    }
}