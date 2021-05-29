<?php
declare(strict_types=1);

namespace Foundation\Container;

final class DependencyCollection extends \ArrayObject {

    public function __construct() {
        parent::__construct();
    }

    public function get(string $abstract): ?Dependency {
        return $this[$abstract] ?? null;
    }

    public function add(Dependency $dependency): void {
        if ($this->has($dependency->getAbstract())) {
            throw new ContainerException(sprintf('Dependency %s is already registered.', $dependency->getAbstract()));
        }

        $this[$dependency->getAbstract()] = $dependency;
    }

    public function remove(string $abstract): void {
        if (!$this->has($abstract)) {
            throw new ContainerException(sprintf('Dependency %s is not registered.', $abstract));
        }

        unset($this[$abstract]);
    }

    public function has(string $abstract): bool {
        return isset($this[$abstract]);
    }
}