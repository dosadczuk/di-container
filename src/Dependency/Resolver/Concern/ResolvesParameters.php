<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Concern;

use Container\Core\Container;
use Container\Core\Dependency\Resolver\Exception\ParameterException;
use Container\Core\Dependency\Resolver\Exception\ParameterNotTypedException;
use Container\Core\Dependency\Resolver\Exception\ParameterWithBuiltinTypeException;
use Container\Core\Dependency\Resolver\Exception\ParameterWithUnionTypeException;

trait ResolvesParameters {

    /**
     * @param \ReflectionParameter[] $parameters
     * @param array<string, object> $defaults
     *
     * @throws \ReflectionException
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
            if (!$parameter_type->isBuiltin()) {
                return Container::get()->make($parameter_type->getName());
            }

            if (!$parameter->isDefaultValueAvailable()) {
                throw new ParameterWithBuiltinTypeException($parameter);
            }

            return $parameter->getDefaultValue();
        }

        throw new ParameterException($parameter);
    }
}