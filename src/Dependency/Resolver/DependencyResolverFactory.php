<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

/**
 * @internal
 */
final class DependencyResolverFactory {

    public function createResolver(mixed $definition): DependencyResolver {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassDependencyResolver($definition);
        }

        if ($definition instanceof \Closure) {
            return new ClosureDependencyResolver($definition);
        }

        throw new DependencyResolverException("Cannot create dependency resolver for '$definition'");
    }
}