<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers;

use Foundation\Container\Resolvers\Exceptions\DependencyResolverException;

interface DependencyResolver {

    /**
     * Resolve dependency with given parameters.
     *
     * @throws DependencyResolverException
     */
    public function resolve(array $parameters = []): object;
}