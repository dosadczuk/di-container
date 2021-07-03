<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Dependency\Resolver\Exception\DependencyResolverException;

interface DependencyResolver {

    /**
     * Resolve dependency with given parameters.
     *
     * @throws DependencyResolverException
     */
    public function resolve(array $parameters = []): object;
}