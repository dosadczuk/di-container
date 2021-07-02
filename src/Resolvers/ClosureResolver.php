<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\Resolvers\Concerns\ResolvesParameters;
use Container\Core\Resolvers\Exceptions\DependencyResolverException;

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