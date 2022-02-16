<?php
declare(strict_types=1);

namespace Container\Core\Resolvers\Concerns;

use Container\Core\Container;
use Container\Core\ContainerException;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
trait ResolvesProperties
{
    /**
     * @param \ReflectionProperty[] $properties
     *
     * @return object[]
     */
    private function resolveProperties(array $properties): array
    {
        return array_map($this->resolveProperty(...), $properties);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function resolveProperty(\ReflectionProperty $property): object
    {
        if (!$property->hasType()) {
            throw new ContainerException("Cannot resolve not typed property '\${$property->getName()}'.");
        }

        $property_type = $property->getType();
        if (!($property_type instanceof \ReflectionNamedType)) {
            throw new ContainerException("Cannot resolve not name typed property '\${$property->getName()}'.");
        }

        if ($property_type->isBuiltin()) {
            throw new ContainerException("Cannot resolve builtin type property '\${$property->getName()}'.");
        }

        return Container::getInstance()->make($property_type->getName());
    }
}
