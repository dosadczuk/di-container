<?php
declare(strict_types=1);

namespace Container\Resolvers\Concerns;

use Container\Container;
use Container\Exceptions\ContainerException;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
trait ResolvesPropertiesTrait
{
    /**
     * @throws ContainerExceptionInterface
     */
    private function resolveProperty(\ReflectionProperty $property, array $arguments = []): mixed
    {
        if (!$property->hasType()) {
            throw new ContainerException("Cannot resolve not typed property '\${$property->getName()}'.");
        }

        // no need to resolve parameter if it's provided by user
        if (array_key_exists($property->getName(), $arguments)) {
            return $arguments[$property->getName()];
        }

        $property_type = $property->getType();
        if ($property_type instanceof \ReflectionIntersectionType) {
            throw new ContainerException("Cannot resolve intersection typed parameter '\${$property->getName()}' without provided value.");
        }

        if ($property_type instanceof \ReflectionUnionType) {
            throw new ContainerException("Cannot resolve union typed parameter '\${$property->getName()}' without provided value.");
        }

        if ($property_type instanceof \ReflectionNamedType && $property_type->isBuiltin()) {
            throw new ContainerException("Cannot resolve builtin typed parameter '\${$property->getName()}' without provided value.");
        }

        // no need to pass $arguments to make() - we handle only one level of arguments providing
        return Container::getInstance()->make($property_type->getName());
    }
}
