<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\ContainerException;
use Container\Core\Resolvers\Concerns\ResolvesParameters;

/**
 * @internal
 */
final class ClosureResolver implements ResolverInterface
{
    use ResolvesParameters;

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
