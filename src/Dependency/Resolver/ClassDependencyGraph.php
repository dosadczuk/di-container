<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

use Container\Core\Attribute\Inject;

final class ClassDependencyGraph {

    private static array $class_adjacency_lists = [];

    public function __construct(
        private string $class_name
    ) {
        $this->createClassAdjacencyList($this->class_name);
    }

    public function isCyclic(): bool {
        $nodes_stacked = [];
        $nodes_visited = [];

        return $this->isClassCyclic(
            $this->class_name,
            $nodes_stacked,
            $nodes_visited
        );
    }

    private function isClassCyclic(string $class_name, array &$stacked, array &$visited): bool {
        if ($stacked[$class_name] ?? false) {
            return true; // cycle found
        }

        if ($visited[$class_name] ?? false) {
            return false; // no cycle
        }

        $stacked[$class_name] = true;
        $visited[$class_name] = true;

        // check children recursively
        foreach (self::$class_adjacency_lists[$class_name] as $adjacent_class_name) {
            if ($this->isClassCyclic($adjacent_class_name, $stacked, $visited)) {
                return true;
            }
        }

        unset($stacked[$class_name]);

        return false;
    }

    private function createClassAdjacencyList(string $class_name): void {
        if (isset(self::$class_adjacency_lists[$class_name])) {
            return; // already created, no need to recreate
        }

        self::$class_adjacency_lists[$class_name] = [];

        try {
            $class = new \ReflectionClass($class_name);

            $this->scanClassProperties($class);
            $this->scanClassMethods($class);

            // create adjacent list for nodes to create graph for $class_name
            foreach (self::$class_adjacency_lists[$class_name] as $adjacent_class_name) {
                $this->createClassAdjacencyList($adjacent_class_name);
            }
        } catch (\ReflectionException $e) {
            throw DependencyResolverException::fromReflectionException($e);
        }
    }

    private function scanClassProperties(\ReflectionClass $class): void {
        foreach ($class->getProperties() as $property) {
            $is_injectable = count($property->getAttributes(Inject::class)) > 0;
            if (!$is_injectable) {
                continue;
            }

            $property_type = $property->getType();
            if (!$property_type instanceof \ReflectionNamedType || $property_type->isBuiltin()) {
                return; // not resolvable
            }

            self::$class_adjacency_lists[$class->getName()][] = $property_type->getName();
        }
    }

    private function scanClassMethods(\ReflectionClass $class): void {
        foreach ($class->getMethods() as $method) {
            $is_injectable = count($method->getAttributes(Inject::class)) > 0;
            if (!$is_injectable && !$method->isConstructor()) {
                continue;
            }

            foreach ($method->getParameters() as $parameter) {
                $parameter_type = $parameter->getType();
                if (!$parameter_type instanceof \ReflectionNamedType || $parameter_type->isBuiltin()) {
                    continue; // not resolvale
                }

                self::$class_adjacency_lists[$class->getName()][] = $parameter_type->getName();
            }
        }
    }
}