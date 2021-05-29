<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Container {

    /**
     * Container instance to be used as singleton.
     */
    private static ?self $instance = null;

    /**
     * Dependencies resolver.
     */
    private DependencyResolver $resolver;

    /**
     * Registered dependencies.
     */
    private DependencyCollection $dependencies;

    private function __construct() {
        $this->resolver = new DependencyResolver();
        $this->dependencies = new DependencyCollection();
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
        if (!$this->dependencies->has($abstract)) {
            return $this->resolver->resolve($abstract, $arguments);
        }

        $dependency = $this->dependencies->get($abstract);
        if ($dependency->isResolved()) {
            return $dependency->getInstance();
        }

        $instance = $this->resolver->resolve($dependency->getConcrete());
        if (!$dependency->isSingleton()) {
            $dependency->setInstance($instance);
        }

        return $instance;
    }

    /**
     * Register dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|null $concrete Optional implementation.
     */
    public function register(string $abstract, string $concrete = null): void {
        $this->dependencies->add(new Dependency($abstract, $concrete, false));
    }

    /**
     * Register dependency as singleton.
     *
     * @param string $abstract Base class/interface.
     * @param string|null $concrete Optional implementation.
     */
    public function registerSingleton(string $abstract, string $concrete = null): void {
        $this->dependencies->add(new Dependency($abstract, $concrete, true));
    }

    /**
     * Unregister dependency.
     *
     * @param string $abstract Base class/interface.
     */
    public function unregister(string $abstract): void {
        $this->dependencies->remove($abstract);
    }

    /**
     * Check if dependency is registered.
     *
     * @param string $abstract Base class/interface.
     */
    public function isRegistered(string $abstract): bool {
        return $this->dependencies->has($abstract);
    }
}