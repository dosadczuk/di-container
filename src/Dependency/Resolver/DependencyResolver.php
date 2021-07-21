<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

/**
 * @internal
 */
interface DependencyResolver {

    /**
     * @throws DependencyResolverException
     */
    public function resolve(array $parameters = []): object;
}