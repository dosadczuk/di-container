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

    public function resolve(): object
    {
        try {
            $closure = new \ReflectionFunction($this->closure);

            $closure_parameters = $this->resolveParameters($closure->getParameters());

            return call_user_func($this->closure, ...$closure_parameters);
        } catch (\ReflectionException $e) {
            throw ContainerException::fromThrowable($e);
        }
    }
}
