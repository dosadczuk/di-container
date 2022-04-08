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