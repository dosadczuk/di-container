<?php
declare(strict_types=1);

namespace Container\Core\Resolver;

use Container\Core\Resolver\Concern\ResolvesParameters;

/**
 * @internal
 */
final class ClosureResolver implements Resolver {

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
            throw ResolverException::fromReflectionException($e);
        }
    }
}