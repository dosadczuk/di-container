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
     * @param array $arguments
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public function resolve(array $arguments = []): mixed;
}
