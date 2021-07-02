<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\Resolvers\Exceptions\DependencyResolverException;

interface DependencyResolver {

    /**
     * Resolve dependency with given parameters.
     *
     * @throws DependencyResolverException
     */
    public function resolve(array $parameters = []): object;
}