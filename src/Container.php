<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Container {

    private static ?self $instance = null;

    private DependencyResolver $resolver;

    private DependencyCollection $dependencies;

    private function __construct() {
        $this->resolver = new DependencyResolver();
        $this->dependencies = new DependencyCollection();
    }

    public static function getInstance(): self {
        return self::$instance
            ?? self::$instance = new self;
    }

    public function make(string $class, array $class_arguments = []): object {
        if ($this->dependencies->has($class)) {
            $dependency = $this->dependencies->get($class);
            if ($dependency->isResolved()) {
                return $dependency->getInstance();
            }

            $dependency_instance = $this->resolver->resolve($dependency->getConcrete());
            if (!$dependency->isSingleton()) {
                $dependency->setInstance($dependency_instance);
            }

            return $dependency_instance;
        }

        return $this->resolver->resolve($class, $class_arguments);
    }

    public function register(string $abstract, string $concrete = null): void {
        $this->dependencies->add(new Dependency($abstract, $concrete, false));
    }

    public function registerSingleton(string $abstract, string $concrete = null): void {
        $this->dependencies->add(new Dependency($abstract, $concrete, true));
    }

    public function unregister(string $abstract): void {
        $this->dependencies->remove($abstract);
    }

    public function isRegistered(string $abstract): bool {
        return $this->dependencies->has($abstract);
    }
}