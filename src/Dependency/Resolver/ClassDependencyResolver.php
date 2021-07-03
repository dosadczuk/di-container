<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Attribute\Inject;
use Container\Core\Dependency\Resolver\Concern\ResolvesParameters;
use Container\Core\Dependency\Resolver\Concern\ResolvesProperties;
use Container\Core\Dependency\Resolver\Exception\DependencyResolverException;

final class ClassDependencyResolver implements DependencyResolver {

    use ResolvesProperties,
        ResolvesParameters;

    public function __construct(
        private string $class_name
    ) {
    }

    public function resolve(array $parameters = []): object {
        try {
            $class = new \ReflectionClass($this->class_name);

            $class_instance = $this->instantiateClass($class, $parameters);
            $this->instantiateClassProperties($class, $class_instance);
            $this->instantiateClassSetters($class, $class_instance);

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
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            if (count($property->getAttributes(Inject::class)) === 0) {
                continue; // no attribute => not injectable
            }

            $property_value = $this->resolveProperty($property);

            $property->setValue($class_instance, $property_value);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClassSetters(\ReflectionClass $class, object $class_instance): void {
        foreach ($class->getMethods() as $method) {
            if (!$method->isPublic()) {
                $method->setAccessible(true);
            }

            if (count($method->getAttributes(Inject::class)) === 0) {
                continue; // no attribute => not injectable
            }

            $method_parameters = $this->resolveParameters($method->getParameters());

            $method->invokeArgs($class_instance, $method_parameters);
        }
    }
}