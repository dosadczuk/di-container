<?php
declare(strict_types=1);

namespace Container\Core\Resolvers;

use Container\Core\ContainerException;

/**
 * @internal
 */
final class ClassGraph
{
    public static array $class_adjacency_lists = [];

    public function __construct(private string $class_name)
    {
        $this->createClassAdjacencyList($class_name);
    }

    public function isCyclic(): bool
    {
        $classes_in_path = [];
        $classes_visited = [];

        return $this->isCyclicClass(
            $this->class_name,
            $classes_in_path,
            $classes_visited
        );
    }

    private function isCyclicClass(string $class_name, array &$in_path, array &$visited): bool
    {
        if ($in_path[$class_name] ?? false) {
            return true; // cycle found
        }

        if ($visited[$class_name] ?? false) {
            return false; // no cycle
        }

        $in_path[$class_name] = true;
        $visited[$class_name] = true;

        foreach (self::$class_adjacency_lists[$class_name] as $adjacent_class_name) {
            if ($this->isCyclicClass($adjacent_class_name, $in_path, $visited)) {
                return true; // cycle found
            }
        }

        unset($in_path[$class_name]);

        return false; // no cycle
    }

    private function createClassAdjacencyList(string $class_name): void
    {
        if (isset(self::$class_adjacency_lists[$class_name])) {
            return; // already created, no need to recreate
        }

        self::$class_adjacency_lists[$class_name] = [];

        try {
            $class = new \ReflectionClass($class_name);

            $this->scanClassProperties($class);
            $this->scanClassMethods($class);

            // create adjacency list for each of dependent classes
            foreach (self::$class_adjacency_lists[$class_name] as $adjacent_class_name) {
                $this->createClassAdjacencyList($adjacent_class_name);
            }
        } catch (\Throwable $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    private function scanClassProperties(\ReflectionClass $class): void
    {
        $class_adjacency_list = &self::$class_adjacency_lists[$class->getName()];

        foreach ($class->getProperties() as $property) {
            if (!ResolverHelper::isInjectable($property)) {
                continue; // not injectable
            }

            $property_type = $property->getType();
            if (!ResolverHelper::isResolvable($property_type)) {
                continue; // not resolvable
            }

            $class_adjacency_list[] = $property_type->getName();
        }
    }

    private function scanClassMethods(\ReflectionClass $class): void
    {
        $class_adjacency_list = &self::$class_adjacency_lists[$class->getName()];

        foreach ($class->getMethods() as $method) {
            if (!ResolverHelper::isInjectable($method)) {
                continue; // not injectable
            }

            foreach ($method->getParameters() as $parameter) {
                $parameter_type = $parameter->getType();
                if (!ResolverHelper::isResolvable($parameter_type)) {
                    continue; // not resolvable
                }

                $class_adjacency_list[] = $parameter_type->getName();
            }
        }
    }
}
