<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

use Foundation\Container\ContainerException;

final class DependencyResolverFactory {

    /**
     * Create dependency resolver for given definition.
     */
    public function createResolver(mixed $definition): DependencyResolver {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassDependencyResolver($definition);
        }

        if (is_callable($definition)) {
            return new ClosureDependencyResolver($definition);
        }

        throw new ContainerException(sprintf('Cannot create dependency resolver for "%s"', $definition));
    }
}