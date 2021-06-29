<?php
declare(strict_types=1);

namespace Foundation\Container;

/**
 * Make instance of given abstract.
 *
 * @param string $abstract Name of the abstract.
 * @param array $parameters Optional parameters.
 *
 * @return object Instance of abstract.
 */
function make(string $abstract, array $parameters = []): object {
    return Container::getInstance()->make($abstract, $parameters);
}

/**
 * Register dependency.
 *
 * @param string $abstract Base class/interface.
 * @param string|\Closure $definition Optional implementation.
 */
function register(string $abstract, string|\Closure $definition): void {
    Container::getInstance()->register($abstract, $definition);
}

/**
 * Register shared dependency.
 *
 * @param string $abstract Base class/interface.
 * @param string|\Closure|null $definition Optional implementation.
 */
function register_shared(string $abstract, string|\Closure $definition = null): void {
    Container::getInstance()->registerShared($abstract, $definition);
}

/**
 * Unregister dependency.
 *
 * @param string $abstract Base class/interface.
 */
function unregister(string $abstract): void {
    Container::getInstance()->unregister($abstract);
}

/**
 * Check if dependency is registered.
 *
 * @param string $abstract Base class/interface.
 */
function is_registered(string $abstract): bool {
    return Container::getInstance()->isRegistered($abstract);
}
