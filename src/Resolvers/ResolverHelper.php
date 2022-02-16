<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\Attributes\Inject;

/**
 * @internal
 */
final class ResolverHelper
{
    public static function isInjectable(\ReflectionProperty|\ReflectionMethod $reflection): bool
    {
        return count($reflection->getAttributes(Inject::class)) > 0;
    }

    public static function isResolvable(\ReflectionType $type): bool
    {
        return $type instanceof \ReflectionNamedType && !$type->isBuiltin();
    }
}
