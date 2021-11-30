<?php
declare(strict_types=1);

namespace Container\Core\Resolver;

/**
 * @internal
 */
interface Resolver {

    /**
     * @throws ResolverException
     */
    public function resolve(array $parameters = []): object;
}