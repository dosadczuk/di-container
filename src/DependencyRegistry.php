<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Resolvers\ResolverFactory;

/**
 * @internal
 */
final class DependencyRegistry extends \ArrayObject
{
	private ResolverFactory $resolver_factory;

	public function __construct() {
		parent::__construct([], 0, \ArrayIterator::class);

		$this->resolver_factory = new ResolverFactory();
	}

	/**
	 * @template T
	 *
	 * @param class-string<T> $abstract
	 *
	 * return T
	 */
	public function get(string $abstract): object {
		if (!$this->has($abstract)) {
			throw ContainerException::notFound($abstract);
		}

		/** @var Dependency $dependency */
		$dependency = $this[$abstract];
		if ($dependency->isInstantiated()) {
			return $dependency->instance;
		}

		$instance = $this->resolve($dependency->definition);

		if ($dependency->is_shared) {
			$dependency->instance = $instance;
		}

		return $instance;
	}

	public function resolve(string|\Closure $definition): object {
		return $this->resolver_factory->createResolver($definition)->resolve();
	}

	public function has(string $abstract): bool {
		return isset($this[$abstract]);
	}

	public function add(Dependency $dependency): void {
		if ($this->has($dependency->abstract)) {
			throw new ContainerException("Dependency '{$dependency->abstract}' is already bound.");
		}

		$this[$dependency->abstract] = $dependency;
	}

	public function remove(string $abstract): void {
		if (!$this->has($abstract)) {
			throw new ContainerException("Dependency '{$abstract}' is not bound.");
		}

		unset($this[$abstract]);
	}

	/**
	 * @param Dependency[] $dependencies
	 */
	public function merge(array $dependencies): void {
		foreach ($dependencies as $dependency) {
			$this->add($dependency);
		}
	}
}
