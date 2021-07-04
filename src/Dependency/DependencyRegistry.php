<?php
declare(strict_types=1);

namespace Container\Core\Dependency;

use Container\Core\ContainerException;
use Container\Core\Dependency\Resolver\DependencyResolverException;
use Container\Core\Dependency\Resolver\DependencyResolverFactory;

final class DependencyRegistry extends \ArrayObject {

    private DependencyResolverFactory $resolver_factory;

    public function __construct() {
        parent::__construct();

        $this->resolver_factory = new DependencyResolverFactory();
    }

    /**
     * Resolve dependency from registry.
     */
    public function make(string|\Closure $abstract, array $parameters = []): object {
        if ($abstract instanceof \Closure || !$this->has($abstract)) {
            return $this->resolve($abstract, $parameters);
        }

        $dependency = $this->get($abstract);
        if ($dependency->isInstantiated()) {
            return $dependency->getInstance();
        }

        $instance = $this->resolve($dependency->getDefinition(), $parameters);

        if ($dependency->isShared()) {
            $dependency->setInstance($instance);
        }

        return $instance;
    }

    private function resolve(string|\Closure $definition, array $parameters): object {
        try {
            return $this->resolver_factory->createResolver($definition)->resolve($parameters);
        } catch (DependencyResolverException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get registered dependency.
     */
    public function get(string $abstract): Dependency {
        if (!$this->has($abstract)) {
            throw new ContainerException("Dependency '$abstract' is not registered");
        }

        return $this[$abstract];
    }

    /**
     * Check if dependency is registered.
     */
    public function has(string $abstract): bool {
        return isset($this[$abstract]);
    }

    /**
     * Fill registry with dependencies (replaces all existing entries).
     *
     * @param Dependency[] $dependencies
     */
    public function set(array $dependencies): void {
        $this->clear();

        foreach ($dependencies as $dependency) {
            $this->add($dependency);
        }
    }

    /**
     * Add dependency to registry.
     */
    public function add(Dependency $dependency): void {
        if ($this->has($dependency->getAbstract())) {
            throw new ContainerException("Dependency '{$dependency->getAbstract()}' is already registered");
        }

        $this[$dependency->getAbstract()] = $dependency;
    }

    /**
     * Remove dependency from registry.
     */
    public function remove(string $abstract): void {
        if (!$this->has($abstract)) {
            throw new ContainerException("Dependency '{$abstract}' is not registered");
        }

        unset($this[$abstract]);
    }

    /**
     * Remove all entries.
     */
    public function clear(): void {
        $this->exchangeArray([]);
    }
}