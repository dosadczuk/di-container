<?php
declare(strict_types=1);

namespace Container\Core\Resolvers\Concerns;

use Container\Core\Container;
use Container\Core\Resolvers\Exceptions\ParameterException;
use Container\Core\Resolvers\Exceptions\ParameterNotTypedException;
use Container\Core\Resolvers\Exceptions\ParameterWithBuiltinTypeException;
use Container\Core\Resolvers\Exceptions\ParameterWithUnionTypeException;

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
                return Container::get($parameter_type->getName());
            }

            if (!$parameter->isDefaultValueAvailable()) {
                throw new ParameterWithBuiltinTypeException($parameter);
            }

            return $parameter->getDefaultValue();
        }

        throw new ParameterException($parameter);
    }
}