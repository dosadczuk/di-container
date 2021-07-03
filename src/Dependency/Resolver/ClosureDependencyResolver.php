<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Dependency\Resolver\Concern\ResolvesParameters;
use Container\Core\Dependency\Resolver\Exception\DependencyResolverException;

final class ClosureDependencyResolver implements DependencyResolver {

    use ResolvesParameters;

    public function __construct(
        private \Closure $closure
    ) {
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