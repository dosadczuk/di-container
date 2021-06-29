<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

interface DependencyResolver {

    /**
     * Resolve dependency with given parameters.
     */
    public function resolve(array $parameters = []): object;
}