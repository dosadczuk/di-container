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

        $property_type = $property->getType();
        if (!($property_type instanceof \ReflectionNamedType) || $property_type->isBuiltin()) {
            throw new ContainerException("Cannot resolve property '\${$property->getName()}'");
        }

        // no need to resolve parameter if it's provided by user
        if (array_key_exists($property->getName(), $arguments)) {
            return $arguments[$property->getName()];
        }

        // no need to pass $arguments to make() - we handle only one level of arguments providing
        return Container::getInstance()->make($property_type->getName());
    }
}
