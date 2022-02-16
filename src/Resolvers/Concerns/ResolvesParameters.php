<?php
declare(strict_types=1);

namespace Container\Core\Resolvers\Concerns;

use Container\Core\Container;
use Container\Core\ContainerException;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
trait ResolvesParameters
{
	/**
	 * @param \ReflectionParameter[] $parameters
	 *
	 * @return object[]
	 */
	private function resolveParameters(array $parameters): array {
		return array_map($this->resolveParameter(...), $parameters);
	}

	/**
	 * @throws ContainerExceptionInterface
	 */
	private function resolveParameter(\ReflectionParameter $parameter): object {
		if (!$parameter->hasType()) {
			throw new ContainerException("Cannot resolve not typed parameter '\${$parameter->getName()}'.");
		}

		$parameter_type = $parameter->getType();
		if (!($parameter_type instanceof \ReflectionNamedType)) {
			throw new ContainerException("Cannot resolve not name typed parameter '\${$parameter->getName()}'.");
		}

		if ($parameter_type->isBuiltin()) {
			throw new ContainerException("Cannot resolve builtin type parameter '\${$parameter->getName()}'.");
		}

		return Container::getInstance()->get($parameter->getName());
	}
}
