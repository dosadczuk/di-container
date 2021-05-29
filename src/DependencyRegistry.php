<?php
declare(strict_types=1);

namespace Foundation\Container;

final class DependencyRegistry extends \ArrayObject {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get registered dependency. Returns {@see null} when not found.
     */
    public function get(string $abstract): ?Dependency {
        return $this[$abstract] ?? null;
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