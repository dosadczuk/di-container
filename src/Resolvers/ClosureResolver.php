<?php
declare(strict_types=1);

namespace Container\Resolvers;

use Container\Exceptions\ContainerException;
use Container\Resolvers\Concerns\ResolvesParametersTrait;

/**
 * @internal
 */
final class ClosureResolver implements ResolverInterface
{
    use ResolvesParametersTrait;

    public function __construct(private \Closure $closure)
    {
    }

    public function resolve(array $arguments = []): object
    {
        try {
            $closure = new \ReflectionFunction($this->closure);

            $arguments = $this->resolveParameters($closure->getParameters(), $arguments);

            return call_user_func($this->closure, ...$arguments);
        } catch (\ReflectionException $e) {
            throw ContainerException::fromThrowable($e);
        }
    }
}
