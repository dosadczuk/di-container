<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
interface ResolverInterface
{
    /**
     * @return object
     * @throws ContainerExceptionInterface
     */
    public function resolve(): object;
}
