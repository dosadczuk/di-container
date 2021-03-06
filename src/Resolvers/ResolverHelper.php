<?php
declare(strict_types=1);

namespace Container\Resolvers;

use Container\Attributes\Inject;

/**
 * @internal
 */
final class ResolverHelper
{
    public static function isInjectable(\ReflectionProperty|\ReflectionMethod $reflection): bool
    {
        if ($reflection instanceof \ReflectionMethod && $reflection->isConstructor()) {
            return true; // constructor is special method, injectable by default
        }

        return count($reflection->getAttributes(Inject::class)) > 0;
    }

    public static function isResolvable(\ReflectionType $type): bool
    {
        return $type instanceof \ReflectionNamedType && !$type->isBuiltin();
    }
}
