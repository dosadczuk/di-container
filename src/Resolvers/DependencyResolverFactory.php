<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\Resolvers\Exceptions\DependencyResolverNotFoundException;

final class DependencyResolverFactory {

    /**
     * Create dependency resolver for given definition.
     */
    public function createResolver(mixed $definition): DependencyResolver {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassResolver($definition);
        }

        if ($definition instanceof \Closure) {
            return new ClosureResolver($definition);
        }

        throw new DependencyResolverNotFoundException($definition);
    }
}