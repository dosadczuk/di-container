<?php
declare(strict_types=1);

namespace Container\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

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

    public function get(string $id)
    {
        return $this->registry->get($id);
    }

    public function has(string $id): bool
    {
        return $this->registry->has($id);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function make(string|\Closure $abstract): object
    {
        return $this->registry->make($abstract);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function bind(string $abstract, string|\Closure $definition): void
    {
        $this->registry->add(Dependency::transient($abstract, $definition));
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function bindShared(string $abstract, string|\Closure $definition = null): void
    {
        $this->registry->add(Dependency::shared($abstract, $definition));
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function unbind(string $abstract): void
    {
        $this->registry->remove($abstract);
    }
}
