<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Container {

    private static ?self $instance = null;

    private DependencyRegistry $registry;

    private DependencyResolver $resolver;

    private function __construct() {
        $this->registry = new DependencyRegistry();
        $this->resolver = new DependencyResolver();
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
    public static function get(string $class, array $parameters = []): object {
        return self::getInstance()->make($class, $parameters);
    }

    /**
     * Make instance of given class.
     *
     * @param string $class Name of the class.
     * @param array $parameters Optional class parameters.
     *
     * @return object Instance of class.
     */
    public function make(string $class, array $parameters = []): object {
        if (!$this->registry->has($class)) {
            return $this->resolver->resolve($class, $parameters);
        }

        $dependency = $this->registry->get($class);
        if ($dependency->hasInstance()) {
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
        $this->registry->add(new Dependency($abstract, $definition, false));
    }

    /**
     * Register shared dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|null $definition Optional implementation.
     */
    public function registerShared(string $abstract, string $definition = null): void {
        $this->registry->add(new Dependency($abstract, $definition, true));
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
    public function registered(string $abstract): bool {
        return $this->registry->has($abstract);
    }
}