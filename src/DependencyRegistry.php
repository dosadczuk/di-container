<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Resolvers\ResolverFactory;
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

    public function add(Dependency $dependency): void
    {
        if ($this->has($dependency->abstract)) {
            throw new ContainerException("Dependency '{$dependency->abstract}' is already bound.");
        }

        $this[$dependency->abstract] = $dependency;
    }

    /**
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
     * @template T
     *
     * @param class-string<T> $abstract
     *
     * @return T
     * @throws ContainerExceptionInterface
     */
    public function make(string $abstract): object
    {
        if (!$this->has($abstract)) {
            return $this->resolve($abstract);
        }

        /** @var Dependency $dependency */
        $dependency = $this[$abstract];
        if ($dependency->isInstantiated()) {
            return $dependency->instance;
        }

        $instance = $this->resolve($dependency->definition);

        if ($dependency->is_shared) {
            $dependency->instance = $instance;
        }

        return $instance;
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function resolve(string|\Closure $definition): object
    {
        return $this->resolver_factory->createResolver($definition)->resolve();
    }

    public function has(string $abstract): bool
    {
        return isset($this[$abstract]);
    }

    public function remove(string $abstract): void
    {
        if (!$this->has($abstract)) {
            throw new ContainerException("Dependency '{$abstract}' is not bound.");
        }

        unset($this[$abstract]);
    }

    /**
     * @param Dependency[] $dependencies
     */
    public function merge(array $dependencies): void
    {
        foreach ($dependencies as $dependency) {
            $this->add($dependency);
        }
    }
}
