<?php
declare(strict_types=1);

namespace Container\Resolvers\Concerns;

use Container\Container;
use Container\Exceptions\ContainerException;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
trait ResolvesParametersTrait
{
    /**
     * @param \ReflectionParameter[] $parameters
     * @param array<string, mixed> $arguments
     *
     * @return array
     * @throws ContainerExceptionInterface
     */
    private function resolveParameters(array $parameters, array $arguments = []): array
    {
        return array_map(
            function (\ReflectionParameter $parameter) use ($arguments) {
                return $this->resolveParameter($parameter, $arguments);
            },
            $parameters
        );
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function resolveParameter(\ReflectionParameter $parameter, array $arguments = []): mixed
    {
        if (!$parameter->hasType()) {
            throw new ContainerException("Cannot resolve not typed parameter '\${$parameter->getName()}'.");
        }

        $parameter_type = $parameter->getType();
        if (!($parameter_type instanceof \ReflectionNamedType) || $parameter_type->isBuiltin()) {
            throw new ContainerException("Cannot resolve parameter '\${$parameter->getName()}'");
        }

        // no need to resolve parameter if it's provided by user
        if (array_key_exists($parameter->getName(), $arguments)) {
            return $arguments[$parameter->getName()];
        }

        // no need to pass $arguments to make() - we handle only one level of arguments providing
        return Container::getInstance()->make($parameter_type->getName());
    }
}
