<?php
declare(strict_types=1);

namespace Foundation\Container\Resolver;

use Foundation\Container\Container;
use Foundation\Container\ContainerException;

trait DependencyResolverTrait {

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

        return Container::get($parameter_type->getName());
    }

    private function resolveBuiltinParameter(\ReflectionParameter $parameter) {
        try {
            return $parameter->getDefaultValue();
        } catch (\Throwable $e) {
            throw ContainerException::fromException($e);
        }
    }

    private function resolveUnionParameter(\ReflectionParameter $parameter): mixed {
        throw new ContainerException(sprintf('Cannot resolve union typed parameter %s', $parameter->getName()));
    }
}