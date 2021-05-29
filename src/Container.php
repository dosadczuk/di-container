<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Container {

    private static ?self $instance = null;

    private DependencyResolver $resolver;

    private DependencyRegistry $registry;

    private function __construct() {
        $this->resolver = new DependencyResolver();
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
     * Make instance of given class.
     *
     * @param string $abstract Name of the class.
     * @param array $arguments Optional class arguments.
     *
     * @return object Instance of class.
     */
    public function make(string $abstract, array $arguments = []): object {
        if (!$this->registry->has($abstract)) {
            return $this->resolver->resolve($abstract, $arguments);
        }

        $dependency = $this->registry->get($abstract);
        if ($dependency->isResolved()) {
            return $dependency->getInstance();
        }

        $instance = $this->resolver->resolve($dependency->getDefinition());
        if ($dependency->isShared()) {
            $dependency->setInstance($instance);
        }

        return $instance;
    }

    /**
     * Register dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|null $definition Optional implementation.
     */
    public function register(string $abstract, string $definition = null): void {
        $this->registry->add(new Dependency($abstract, $definition, true));
    }

    /**
     * Register dependency as singleton.
     *
     * @param string $abstract Base class/interface.
     * @param string|null $definition Optional implementation.
     */
    public function registerSingleton(string $abstract, string $definition = null): void {
        $this->registry->add(new Dependency($abstract, $definition, false));
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