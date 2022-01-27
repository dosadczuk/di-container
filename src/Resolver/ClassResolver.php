<?php
declare(strict_types=1);

namespace Container\Core\Resolver;

use Container\Core\Attribute\Inject;
use Container\Core\Resolver\Concern\ResolvesParameters;
use Container\Core\Resolver\Concern\ResolvesProperties;

/**
 * @internal
 */
final class ClassResolver implements Resolver {

    use ResolvesProperties,
        ResolvesParameters;

    private ClassGraph $graph;

    public function __construct(
        private string $class_name
    ) {
        $this->graph = new ClassGraph($class_name);
    }

    public function resolve(array $parameters = []): object {
        if ($this->graph->isCyclic()) {
            throw new ResolverException(
                "$this->class_name contains cyclic dependencies"
            );
        }

        try {
            $class = new \ReflectionClass($this->class_name);

            $class_instance = $this->instantiateClass($class, $parameters);
            $this->instantiateClassProperties($class, $class_instance);
            $this->instantiateClassMethods($class, $class_instance);

            return $class_instance;
        } catch (\ReflectionException $e) {
            throw ResolverException::fromReflectionException($e);
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

            $property->setValue($class_instance, $this->resolveProperty($property));
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

            $method->invokeArgs($class_instance, $this->resolveParameters($method->getParameters()));
        }
    }
}