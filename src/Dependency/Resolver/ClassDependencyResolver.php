<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Attribute\Inject;
use Container\Core\Dependency\Resolver\Concern\ResolvesParameters;
use Container\Core\Dependency\Resolver\Concern\ResolvesProperties;

final class ClassDependencyResolver implements DependencyResolver {

    use ResolvesProperties,
        ResolvesParameters;

    private ClassDependencyGraph $dependency_graph;

    public function __construct(
        private string $class_name
    ) {
        $this->dependency_graph = new ClassDependencyGraph($class_name);
    }

    public function resolve(array $parameters = []): object {
        if ($this->dependency_graph->isCyclic()) {
            throw new DependencyResolverException(
                "{$this->class_name} contains cyclic dependencies"
            );
        }

        try {
            $class = new \ReflectionClass($this->class_name);

            $class_instance = $this->instantiateClass($class, $parameters);
            $this->instantiateClassProperties($class, $class_instance);
            $this->instantiateClassMethods($class, $class_instance);

            return $class_instance;
        } catch (\ReflectionException $e) {
            throw DependencyResolverException::fromReflectionException($e);
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
            $is_injectable = count($property->getAttributes(Inject::class)) > 0;
            if (!$is_injectable) {
                continue;
            }

            $property_value = $this->resolveProperty($property);

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $property->setValue($class_instance, $property_value);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClassMethods(\ReflectionClass $class, object $class_instance): void {
        foreach ($class->getMethods() as $method) {
            $is_injectable = count($method->getAttributes(Inject::class)) > 0;
            if (!$is_injectable) {
                continue;
            }

            $method_parameters = $this->resolveParameters($method->getParameters());

            if (!$method->isPublic()) {
                $method->setAccessible(true);
            }

            $method->invokeArgs($class_instance, $method_parameters);
        }
    }
}