<?php
declare(strict_types=1);

namespace Container\Core\Resolver;

/**
 * @internal
 */
final class ResolverFactory {

    public function createResolver(mixed $definition): Resolver {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassResolver($definition);
        }

        if ($definition instanceof \Closure) {
            return new ClosureResolver($definition);
        }

        throw new ResolverException("Cannot create dependency resolver for '$definition'");
    }
}