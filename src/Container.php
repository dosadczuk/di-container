<?php
declare(strict_types=1);

namespace Foundation\Container;

use Foundation\Container\Exception\ContainerResolveException;
use JetBrains\PhpStorm\ArrayShape;

final class Container implements \ArrayAccess {

    private static ?self $instance = null;

    #[ArrayShape([
        'abstract' => 'string',
        'concrete' => 'string|null',
        'shared'   => 'bool',
    ])]
    private array $bindings = [];

    #[ArrayShape([
        'class-string' => 'string',
    ])]
    private array $instances = [];

    private function __construct() {
    }

    public static function getInstance(): self {
        return self::$instance
            ?? self::$instance = new self;
    }

    public function make(string $abstract, array $parameters = []): object {
        return $this->resolve($abstract, $parameters);
    }

    public function bind(string $abstract, string $concrete = null, bool $is_singleton = false): void {
        // TODO: Implement bind() method.
    }

    public function bound(string $abstract): bool {
        // TODO: Implement bound() method.
    }

    public function unbind(string $abstract): void {
        // TODO: Implement unbind() method.
    }

    public function instance(string $abstract, string $concrete = null): void {
        // TODO: Implement instance() method.
    }

    public function singleton(string $abstract, string $concrete = null): void {
        // TODO: Implement singleton() method.
    }

    private function resolve(string $abstract, array $parameters = []): object {
        try {
            $instance_class = new \ReflectionClass($abstract);

            $instance_constructor = $instance_class->getConstructor();
            if ($instance_constructor === null) {
                return $instance_class->newInstanceWithoutConstructor();
            }

            $instance_args = [];
            foreach ($instance_constructor->getParameters() as $parameter) {
                $instance_args[] = $this->resolveParameter($parameter, $parameters);
            }

            return $instance_class->newInstance(...$instance_args);
        } catch (\Throwable $e) {
            throw new ContainerResolveException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function resolveParameter(\ReflectionParameter $parameter, array $parameters_arguments = []): mixed {
        if (array_key_exists($parameter->getName(), $parameters_arguments)) {
            return $parameters_arguments[$parameter->getName()]; // is predefined, so take it
        }

        if (!$parameter->hasType()) {
            throw new ContainerResolveException('Cannot resolve not typed parameter "%s"', $parameter->getName());
        }

        if ($parameter->getType() instanceof \ReflectionNamedType) {
            return $this->resolveNamedParameter($parameter);
        }

        if ($parameter->getType() instanceof \ReflectionUnionType) {
            return $this->resolveUnionParameter($parameter);
        }

        throw new ContainerResolveException('Cannot resolve parameter "%s"', $parameter->getName());
    }

    private function resolveNamedParameter(\ReflectionParameter $parameter): mixed {
        /** @var \ReflectionNamedType $parameter_type */
        $parameter_type = $parameter->getType();

        if ($parameter_type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new ContainerResolveException('Cannot resolve builtin parameter "%s"', $parameter->getName());
        }

        $class_name = $parameter_type->getName();
        if (!class_exists($class_name)) {
            throw new ContainerResolveException('Cannot resolve class parameter "%s"', $parameter->getName());
        }

        return $this->resolve($class_name);
    }

    private function resolveUnionParameter(\ReflectionParameter $parameter): mixed {
        throw new ContainerResolveException('Cannot resolve union parameter "%s"', $parameter->getName());
    }

    public function offsetExists($key): bool {
        return $this->bound($key);
    }

    public function offsetGet($key) {
        return $this->resolve($key);
    }

    public function __get(string $key) {
        return $this[$key];
    }

    public function offsetSet($key, $value) {
        $this->bind($key, $value);
    }

    public function __set(string $key, $value): void {
        $this[$key] = $value;
    }

    public function offsetUnset($key) {
        $this->unbind($key);
    }

    public function __unset(string $key): void {
        unset($this[$key]);
    }
}