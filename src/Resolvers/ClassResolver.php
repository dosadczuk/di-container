<?php
declare(strict_types=1);

namespace Container\Resolvers;

use Container\Exceptions\ContainerException;
use Container\Exceptions\DependencyCycleException;
use Container\Resolvers\Concerns\ResolvesParametersTrait;
use Container\Resolvers\Concerns\ResolvesPropertiesTrait;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
final class ClassResolver implements ResolverInterface
{
    use ResolvesPropertiesTrait,
        ResolvesParametersTrait;

    private ClassGraph $graph;

    public function __construct(private string $class_name)
    {
        $this->graph = new ClassGraph($class_name);
    }

    public function resolve(): object
    {
        if ($this->graph->isCyclic()) {
            throw new DependencyCycleException($this->class_name);
        }

        try {
            $class = new \ReflectionClass($this->class_name);

            $class_instance = $this->instantiateClass($class);
            $this->instantiateClassProperties($class, $class_instance);
            $this->instantiateClassMethods($class, $class_instance);

            return $class_instance;
        } catch (\ReflectionException $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClass(\ReflectionClass $class): object
    {
        if (($constructor = $class->getConstructor()) === null) {
            return $class->newInstanceWithoutConstructor();
        }

        $constructor_parameters = $this->resolveParameters($constructor->getParameters());

        return $class->newInstanceArgs($constructor_parameters);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function instantiateClassProperties(\ReflectionClass $class, $class_instance): void
    {
        foreach ($class->getProperties() as $property) {
            if (!ResolverHelper::isInjectable($property)) {
                continue; // not injectable
            }

            $property->setValue($class_instance, $this->resolveProperty($property));
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function instantiateClassMethods(\ReflectionClass $class, $class_instance): void
    {
        foreach ($class->getMethods() as $method) {
            if (!ResolverHelper::isInjectable($method)) {
                continue; // not injectable
            }

            $method_parameters = $this->resolveParameters($method->getParameters());

            $method->invokeArgs($class_instance, $method_parameters);
        }
    }
}
