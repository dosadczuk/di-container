<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

use Foundation\Container\ContainerException;

final class ClosureDependencyResolver implements DependencyResolver {

    use DependencyResolverTrait;

    private \Closure $closure;

    public function __construct(\Closure $closure) {
        $this->closure = $closure;
    }

    public function resolve(array $parameters = []): object {
        try {
            $closure = new \ReflectionFunction($this->closure);

            $closure_parameters = array_map(
                function (\ReflectionParameter $parameter) {
                    return $this->resolveParameter($parameter);
                },
                $closure->getParameters()
            );

            return call_user_func($this->closure, ...$closure_parameters);
        } catch (\ReflectionException $e) {
            throw ContainerException::fromException($e);
        }
    }
}