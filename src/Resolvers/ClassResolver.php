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

    /**
     * @template T
     *
     * @param class-string<T> $class_name
     */
    public function __construct(private string $class_name)
    {
        $this->graph = new ClassGraph($class_name);
    }

    public function resolve(array $arguments = []): object
    {
        if ($this->graph->isCyclic()) {
            throw new DependencyCycleException($this->class_name);
        }

        try {
            $class = new \ReflectionClass($this->class_name);

            $instance = $this->instantiateClass($class, $arguments);
            $this->instantiateClassProperties($class, $instance, $arguments);
            $this->instantiateClassMethods($class, $instance, $arguments);

            return $instance;
        } catch (\ReflectionException $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * @throws \ReflectionException|ContainerExceptionInterface
     */
    private function instantiateClass(\ReflectionClass $class, array $class_arguments = []): object
    {
        if (($constructor = $class->getConstructor()) === null) {
            return $class->newInstanceWithoutConstructor();
        }

        $arguments = $this->resolveParameters($constructor->getParameters(), $class_arguments);

        return $class->newInstanceArgs($arguments);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function instantiateClassProperties(\ReflectionClass $class, object $class_instance, array $class_arguments = []): void
    {
        foreach ($class->getProperties() as $property) {
            if (!ResolverHelper::isInjectable($property)) {
                continue; // not injectable
            }

            $argument = $this->resolveProperty($property, $class_arguments);

            $property->setValue($class_instance, $argument);
        }
    }

    /**
     * @throws \ReflectionException|ContainerExceptionInterface
     */
    private function instantiateClassMethods(\ReflectionClass $class, object $class_instance, array $class_arguments = []): void
    {
        foreach ($class->getMethods() as $method) {
            if (!ResolverHelper::isInjectable($method)) {
                continue; // not injectable
            }

            $arguments = $this->resolveParameters($method->getParameters(), $class_arguments);

            $method->invokeArgs($class_instance, $arguments);
        }
    }
}
