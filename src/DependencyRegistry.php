<?php
declare(strict_types=1);

namespace Foundation\Container;

use Foundation\Container\Resolvers\DependencyResolverFactory;
use Foundation\Container\Resolvers\Exceptions\DependencyResolverException;

final class DependencyRegistry extends \ArrayObject {

    private DependencyResolverFactory $resolver_factory;

    public function __construct() {
        parent::__construct();

        $this->resolver_factory = new DependencyResolverFactory();
    }

    /**
     * Resolve dependency from registry.
     */
    public function make(string|\Closure $abstract, array $parameters): object {
        if (!$this->has($abstract)) {
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
            throw ContainerException::fromThrowable($e);
        }
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