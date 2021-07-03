<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Dependency\Resolver\Exception\DependencyResolverNotFoundException;

final class DependencyResolverFactory {

    /**
     * Create dependency resolver for given definition.
     */
    public function createResolver(mixed $definition): DependencyResolver {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassDependencyResolver($definition);
        }

        if ($definition instanceof \Closure) {
            return new ClosureDependencyResolver($definition);
        }

        throw new DependencyResolverNotFoundException($definition);
    }
}