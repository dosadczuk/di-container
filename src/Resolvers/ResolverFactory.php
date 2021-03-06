<?php
declare(strict_types=1);

namespace Container\Resolvers;

use Container\Exceptions\ContainerException;

/**
 * @internal
 */
final class ResolverFactory
{
    public function createResolver(mixed $definition): ResolverInterface
    {
        if (is_string($definition) && class_exists($definition)) {
            return new ClassResolver($definition);
        }

        if ($definition instanceof \Closure) {
            return new ClosureResolver($definition);
        }

        throw new ContainerException("Cannot create resolver for '{$definition}'.");
    }
}
