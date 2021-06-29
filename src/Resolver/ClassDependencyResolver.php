<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

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

            if (($constructor = $class->getConstructor()) === null) {
                return $class->newInstanceWithoutConstructor();
            }

            $class_parameters = array_map(
                function (\ReflectionParameter $parameter) use ($parameters) {
                    if (isset($parameters[$parameter->getName()])) {
                        return $parameters[$parameter->getName()];
                    }

                    return $this->resolveParameter($parameter);
                },
                $constructor->getParameters()
            );

            return $class->newInstanceArgs($class_parameters);
        } catch (\ReflectionException $e) {
            throw ContainerException::fromException($e);
        }
    }
}