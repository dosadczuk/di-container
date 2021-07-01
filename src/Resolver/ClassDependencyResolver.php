<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

use Foundation\Container\Attribute\Inject;
use Foundation\Container\Container;
use Foundation\Container\ContainerException;

final class ClassDependencyResolver implements DependencyResolver {

    use DependencyResolverTrait;

    private string $class_name;

    public function __construct(string $class_name) {
        $this->class_name = $class_name;
    }

    public function resolve(array $parameters = []): object {
        try {
            $class = new \ReflectionClass($this->class_name);

            $class_instance = $this->instantiateClass($class, $parameters);
            $this->instantiateClassProperties($class, $class_instance);
            $this->instantiateClassSetters($class, $class_instance);

            return $class_instance;
        } catch (\ReflectionException $e) {
            throw ContainerException::fromException($e);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClass(\ReflectionClass $class, array $parameters): object {
        if (($constructor = $class->getConstructor()) === null) {
            return $class->newInstanceWithoutConstructor();
        }

        $constructor_parameters = $this->resolveParameters(
            $constructor->getParameters(),
            $parameters
        );

        return $class->newInstanceArgs($constructor_parameters);
    }

    private function instantiateClassProperties(\ReflectionClass $class, object $class_instance): void {
        foreach ($class->getProperties() as $property) {
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            if ($property->isInitialized($class_instance)) {
                continue; // injection not required
            }

            $attribute = $property->getAttributes(Inject::class)[0] ?? null;
            if ($attribute === null) {
                continue; // not injectable
            }

            $property_value = $this->resolveProperty($property);

            $property->setValue($class_instance, $property_value);
        }
    }

    private function resolveProperty(\ReflectionProperty $property): mixed {
        if (!$property->hasType()) {
            throw new ContainerException(sprintf('Cannot resolve not typed property %s', $property->getName()));
        }

        if ($property->getType() instanceof \ReflectionNamedType) {
            return $this->resolveNamedProperty($property);
        }

        if ($property->getType() instanceof \ReflectionUnionType) {
            return $this->resolveUnionProperty($property);
        }

        throw new ContainerException(sprintf('Cannot resolve property %s', $property->getName()));
    }

    private function resolveNamedProperty(\ReflectionProperty $property): mixed {
        /** @var \ReflectionNamedType $property_type */
        $property_type = $property->getType();
        if ($property_type->isBuiltin()) {
            return $this->resolveBuiltinProperty($property);
        }

        return Container::get($property_type->getName());
    }

    private function resolveBuiltinProperty(\ReflectionProperty $property): mixed {
        throw new ContainerException(sprintf('Cannot resolve builtin typed property %s', $property->getName()));
    }

    private function resolveUnionProperty(\ReflectionProperty $property): mixed {
        throw new ContainerException(sprintf('Cannot resolve union typed property %s', $property->getName()));
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClassSetters(\ReflectionClass $class, object $class_instance) {
        foreach ($class->getMethods() as $method) {
            if (!$method->isPublic()) {
                $method->setAccessible(true);
            }

            $attribute = $method->getAttributes(Inject::class)[0] ?? null;
            if ($attribute === null) {
                continue; // not injectable
            }

            $method_parameters = $this->resolveParameters($method->getParameters());

            $method->invokeArgs($class_instance, $method_parameters);
        }
    }
}