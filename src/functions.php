<?php
declare(strict_types=1);

namespace Container\Core;

/**
 * Get Container instance.
 *
 * @api
 */
function container(): Container {
    return Container::getInstance();
}

/**
 * Make instance of given abstract.
 *
 * @template T
 *
 * @param class-string<T> $abstract Name of the abstract.
 * @param array $parameters Optional parameters.
 *
 * @return T Instance of abstract.
 *
 * @api
 */
function make(string $abstract, array $parameters = []): object {
    return Container::getInstance()->make($abstract, $parameters);
}

/**
 * Register dependency.
 *
 * @param class-string $abstract Base class/interface.
 * @param string|\Closure $definition Optional implementation.
 *
 * @api
 */
function register(string $abstract, string|\Closure $definition): void {
    Container::getInstance()->register($abstract, $definition);
}

/**
 * Register shared dependency.
 *
 * @param class-string $abstract Base class/interface.
 * @param string|\Closure|null $definition Optional implementation.
 *
 * @api
 */
function register_shared(string $abstract, string|\Closure $definition = null): void {
    Container::getInstance()->registerShared($abstract, $definition);
}

/**
 * Unregister dependency.
 *
 * @param class-string $abstract Base class/interface.
 *
 * @api
 */
function unregister(string $abstract): void {
    Container::getInstance()->unregister($abstract);
}

/**
 * Check if dependency is registered.
 *
 * @param class-string $abstract Base class/interface.
 *
 * @api
 */
function is_registered(string $abstract): bool {
    return Container::getInstance()->isRegistered($abstract);
}
