<?php
declare(strict_types=1);

namespace Foundation\Container;

use Foundation\Container\Resolver\DependencyResolverFactory;

final class DependencyRegistry extends \ArrayObject {

    private DependencyResolverFactory $resolver_factory;

    public function __construct() {
        parent::__construct();

        $this->resolver_factory = new DependencyResolverFactory();
    }

    public function resolve(string|\Closure $abstract, array $parameters): object {
        if (!$this->has($abstract)) {
            return $this->resolver_factory
                ->createResolver($abstract)
                ->resolve($parameters);
        }

        $dependency = $this->get($abstract);
        if ($dependency->hasInstance()) {
            return $dependency->getInstance();
        }

        $dependency_instance = $this->resolver_factory
            ->createResolver($dependency->getDefinition())
            ->resolve($parameters);

        if ($dependency->isShared()) {
            $dependency->setInstance($dependency_instance);
        }

        return $dependency_instance;
    }

    /**
     * Get registered dependency.
     */
    public function get(string $abstract): Dependency {
        if (!$this->has($abstract)) {
            throw new ContainerException(sprintf('Dependency %s is not registered.', $abstract));
        }

        return $this[$abstract];
    }

    /**
     * Add dependency to registry.
     */
    public function add(Dependency $dependency): void {
        if ($this->has($dependency->getAbstract())) {
            throw new ContainerException(sprintf('Dependency %s is already registered.', $dependency->getAbstract()));
        }

        $this[$dependency->getAbstract()] = $dependency;
    }

    /**
     * Remove dependency from registry.
     */
    public function remove(string $abstract): void {
        if (!$this->has($abstract)) {
            throw new ContainerException(sprintf('Dependency %s is not registered.', $abstract));
        }

        unset($this[$abstract]);
    }

    /**
     * Check if dependency is registered.
     */
    public function has(string $abstract): bool {
        return isset($this[$abstract]);
    }
}