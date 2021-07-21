<?php
declare(strict_types=1);

namespace Container\Core\Dependency;

use Container\Core\Dependency\Resolver\DependencyResolverFactory;

/**
 * @internal
 */
final class DependencyRegistry extends \ArrayObject {

    private DependencyResolverFactory $resolver_factory;

    public function __construct() {
        parent::__construct();

        $this->resolver_factory = new DependencyResolverFactory();
    }

    public function make(string $abstract, array $parameters = []): object {
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
        return $this->resolver_factory->createResolver($definition)->resolve($parameters);
    }

    public function get(string $abstract): Dependency {
        if (!$this->has($abstract)) {
            throw new \InvalidArgumentException("Dependency '$abstract' is not registered");
        }

        return $this[$abstract];
    }

    public function has(string $abstract): bool {
        return isset($this[$abstract]);
    }

    public function add(Dependency $dependency): void {
        if ($this->has($dependency->getAbstract())) {
            throw new \InvalidArgumentException("Dependency '{$dependency->getAbstract()}' is already registered");
        }

        $this[$dependency->getAbstract()] = $dependency;
    }

    public function remove(string $abstract): void {
        if (!$this->has($abstract)) {
            throw new \InvalidArgumentException("Dependency '{$abstract}' is not registered");
        }

        unset($this[$abstract]);
    }

    /**
     * @param Dependency[] $dependencies
     */
    public function merge(array $dependencies): void {
        foreach ($dependencies as $dependency) {
            $this[$dependency->getAbstract()] = $dependency;
        }
    }
}