<?php
declare(strict_types=1);

namespace Foundation\Container;

final class DependencyResolver {

    /**
     * Resolve dependency by given name and arguments.
     *
     * @param string $class_name
     * @param array $class_parameters
     *
     * @return object
     */
    public function resolve(string $class_name, array $class_parameters = []): object {
        try {
            $class = new \ReflectionClass($class_name);

            $constructor = $class->getConstructor();
            if ($constructor === null) {
                return $class->newInstanceWithoutConstructor();
            }

            $parameters = [];
            foreach ($constructor->getParameters() as $parameter) {
                if (array_key_exists($parameter->getName(), $class_parameters)) {
                    $parameters[] = $class_parameters[$parameter->getName()];
                }
                else {
                    $parameters[] = $this->resolveParameter($parameter);
                }
            }

            return $class->newInstance(...$parameters);
        } catch (\ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function resolveParameter(\ReflectionParameter $parameter): mixed {
        if (!$parameter->hasType()) {
            throw new ContainerException(sprintf('Cannot resolve not typed parameter %s.', $parameter->getName()));
        }

        if ($parameter->getType() instanceof \ReflectionNamedType) {
            return $this->resolveNamedParameter($parameter);
        }

        if ($parameter->getType() instanceof \ReflectionUnionType) {
            return $this->resolveUnionParameter($parameter);
        }

        throw new ContainerException(sprintf('Cannot resolve parameter %s', $parameter->getName()));
    }

    private function resolveNamedParameter(\ReflectionParameter $parameter): mixed {
        /** @var \ReflectionNamedType $parameter_type */
        $parameter_type = $parameter->getType();
        if ($parameter_type->isBuiltin()) {
            return $this->resolveBuiltinParameter($parameter);
        }

        return $this->resolveClassParameter($parameter);
    }

    private function resolveBuiltinParameter(\ReflectionParameter $parameter): mixed {
        try {
            return $parameter->getDefaultValue();
        } catch (\Throwable $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function resolveClassParameter(\ReflectionParameter $parameter): object {
        $class_name = $parameter->getType()->getName();
        if (!class_exists($class_name)) {
            throw new ContainerException(sprintf('Cannot resolve not existing class %s', $class_name));
        }

        return $this->resolve($class_name);
    }

    private function resolveUnionParameter(\ReflectionParameter $parameter): mixed {
        throw new ContainerException(sprintf('Cannot resolve union typed parameter %s', $parameter->getName()));
    }
}