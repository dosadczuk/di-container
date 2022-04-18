<?php
declare(strict_types=1);

namespace Container;

use Container\Exceptions\DependencyAlreadyAddedException;
use Container\Exceptions\DependencyNotFoundException;
use Container\Resolvers\ResolverFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
final class DependencyRegistry extends \ArrayObject
{
    private ResolverFactory $resolver_factory;

    public function __construct()
    {
        parent::__construct([], 0, \ArrayIterator::class);

        $this->resolver_factory = new ResolverFactory();
    }

    /**
     * Add dependency to registry.
     */
    public function add(Dependency $dependency): void
    {
        if ($this->has($dependency->abstract)) {
            throw new DependencyAlreadyAddedException($dependency->abstract);
        }

        $this[$dependency->abstract] = $dependency;
    }

    /**
     * Get instance of given abstract.
     *
     * @template T
     *
     * @param class-string<T> $abstract
     *
     * @return T
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function get(string $abstract): object
    {
        if (!$this->has($abstract)) {
            throw new DependencyNotFoundException($abstract);
        }

        return $this->make($abstract);
    }

    /**
     * Make instance of given abstract.
     *
     * @template T
     *
     * @param class-string<T> $abstract
     * @param array<string, mixed> $arguments
     *
     * @return T
     * @throws ContainerExceptionInterface
     */
    public function make(string $abstract, array $arguments = []): object
    {
        if (!$this->has($abstract)) {
            return $this->resolve($abstract, $arguments);
        }

        /** @var Dependency $dependency */
        $dependency = $this[$abstract];
        if ($dependency->isInstantiated()) {
            if (count($arguments) > 0) {
                trigger_error("[Container]: Providing arguments to already instantiated shared dependency has no effect.", E_USER_WARNING);
            }

            return $dependency->instance;
        }

        // provided $arguments can override some of $dependency->arguments, and it is intended behavior
        $instance = $this->resolve($dependency->definition, [...$dependency->arguments, ...$arguments]);

        if ($dependency->is_shared) {
            $dependency->instance = $instance;
        }

        return $instance;
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function resolve(string|\Closure $definition, array $arguments = []): object
    {
        return $this->resolver_factory->createResolver($definition)->resolve($arguments);
    }

    /**
     * Check if registry has given abstract.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     */
    public function has(string $abstract): bool
    {
        return isset($this[$abstract]);
    }

    /**
     * Remove given abstract from registry.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     */
    public function remove(string $abstract): void
    {
        if (!$this->has($abstract)) {
            throw new DependencyNotFoundException($abstract);
        }

        unset($this[$abstract]);
    }
}
