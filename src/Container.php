<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Container {

    private static ?self $instance = null;

    private DependencyRegistry $registry;

    private function __construct() {
        $this->registry = new DependencyRegistry();
    }

    /**
     * Get instance of Container.
     */
    public static function getInstance(): self {
        return self::$instance
            ?? self::$instance = new self;
    }

    /**
     * Static version of {@see Container::make()}.
     */
    public static function get(string $abstract, array $parameters = []): object {
        return self::getInstance()->make($abstract, $parameters);
    }

    /**
     * Make instance of given abstract.
     *
     * @param string $abstract Name of the abstract.
     * @param array $parameters Optional parameters.
     *
     * @return object Instance of abstract.
     */
    public function make(string $abstract, array $parameters = []): object {
        return $this->registry->make($abstract, $parameters);
    }

    /**
     * Register dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|\Closure|null $definition Optional implementation.
     */
    public function register(string $abstract, string|\Closure $definition = null): void {
        $this->registry->add(Dependency::normal($abstract, $definition));
    }

    /**
     * Register shared dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|\Closure|null $definition Optional implementation.
     */
    public function registerShared(string $abstract, string|\Closure $definition = null): void {
        $this->registry->add(Dependency::shared($abstract, $definition));
    }

    /**
     * Unregister dependency.
     *
     * @param string $abstract Base class/interface.
     */
    public function unregister(string $abstract): void {
        $this->registry->remove($abstract);
    }

    /**
     * Check if dependency is registered.
     *
     * @param string $abstract Base class/interface.
     */
    public function isRegistered(string $abstract): bool {
        return $this->registry->has($abstract);
    }
}