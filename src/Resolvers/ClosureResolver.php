<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers;

use Foundation\Container\Resolvers\Concerns\ResolvesParameters;
use Foundation\Container\Resolvers\Exceptions\DependencyResolverException;

final class ClosureResolver implements DependencyResolver {

    use ResolvesParameters;

    private \Closure $closure;

    public function __construct(\Closure $closure) {
        $this->closure = $closure;
    }

    public function resolve(array $parameters = []): object {
        try {
            $closure = new \ReflectionFunction($this->closure);

            $closure_parameters = $this->resolveParameters(
                $closure->getParameters(),
                $parameters
            );

            return call_user_func($this->closure, ...$closure_parameters);
        } catch (\ReflectionException $e) {
            throw DependencyResolverException::fromReflectionException($e);
        }
    }
}