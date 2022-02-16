<?php
declare(strict_types=1);

namespace Container\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Container implements ContainerInterface
{
    /**
     * Container instance (singleton).
     */
    private static ?self $instance = null;

    /**
     * Registry with dependencies.
     */
    private DependencyRegistry $registry;

    private function __construct()
    {
        $this->registry = new DependencyRegistry();
    }

    /**
     * Get instance of Container.
     *
     * @api
     */
    public static function getInstance(): self
    {
        return self::$instance
            ?? self::$instance = new self();
    }

    /**
     * Get instance of given abstract.
     *
     * @template T
     *
     * @param class-string<T> $id Class/Interface.
     *
     * @return T Instance of id.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @api
     */
    public function get(string $id): mixed
    {
        return $this->registry->get($id);
    }

    /**
     * Check if it has dependency.
     *
     * @param class-string $id Class/Interface.
     *
     * @api
     */
    public function has(string $id): bool
    {
        return $this->registry->has($id);
    }

    /**
     * Make instance of given abstract.
     *
     * @template T
     *
     * @param class-string<T> $abstract Class/Interface.
     *
     * @return T Instance of abstract.
     * @throws ContainerExceptionInterface
     *
     * @api
     */
    public function make(string $abstract): object
    {
        return $this->registry->make($abstract);
    }

    /**
     * Bind abstract with definition to container.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     * @param string|\Closure $definition Implementation or factory function.
     *
     * @throws ContainerExceptionInterface
     *
     * @api
     */
    public function bind(string $abstract, string|\Closure $definition): void
    {
        $this->registry->add(Dependency::transient($abstract, $definition));
    }

    /**
     * Bind abstract with definition to container, as singleton.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     * @param null|string|\Closure $definition Optional implementation or factory function.
     *
     * @throws ContainerExceptionInterface
     *
     * @api
     */
    public function bindShared(string $abstract, string|\Closure $definition = null): void
    {
        $this->registry->add(Dependency::shared($abstract, $definition));
    }

    /**
     * Unbind abstract from container.
     *
     * @template T
     *
     * @param class-string<T> $abstract Abstract/Interface.
     *
     * @throws ContainerExceptionInterface
     *
     * @api
     */
    public function unbind(string $abstract): void
    {
        $this->registry->remove($abstract);
    }
}
