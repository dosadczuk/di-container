<?php
declare(strict_types=1);

namespace Container\Resolvers;

use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
interface ResolverInterface
{
    /**
     * Resolve dependency.
     *
     * @return object
     * @throws ContainerExceptionInterface
     */
    public function resolve(): object;
}
