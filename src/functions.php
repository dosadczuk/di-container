<?php
declare(strict_types=1);

namespace Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Get instance of Container.
 *
 * @api
 */
function container(): Container
{
    return Container::getInstance();
}

/**
 * Get instance of given abstract.
 *
 * @template T
 *
 * @param class-string<T> $id Class/Interface.
 *
 * @return T Instance of id.
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 *
 * @api
 */
function get(string $id): object
{
    return container()->get($id);
}

/**
 * Check if it has dependency.
 *
 * @param class-string $id Class/Interface.
 *
 * @api
 */
function has(string $id): bool
{
    return container()->has($id);
}

/**
 * Make instance of given abstract.
 *
 * @template T
 *
 * @param class-string<T> $abstract Class/Interface.
 *
 * @return T Instance of abstract.
 * @throws ContainerExceptionInterface
 *
 * @api
 */
function make(string $abstract): object
{
    return container()->make($abstract);
}

/**
 * Bind abstract with definition to container.
 *
 * @template T
 *
 * @param class-string<T> $abstract Abstract/Interface.
 * @param string|\Closure $definition Implementation or factory function.
 *
 * @throws ContainerExceptionInterface
 *
 * @api
 */
function bind(string $abstract, string|\Closure $definition): void
{
    container()->bind($abstract, $definition);
}

/**
 * Bind abstract with definition to container, as singleton.
 *
 * @template T
 *
 * @param class-string<T> $abstract Abstract/Interface.
 * @param null|string|\Closure $definition Optional implementation or factory function.
 *
 * @throws ContainerExceptionInterface
 *
 * @api
 */
function bind_shared(string $abstract, string|\Closure $definition = null): void
{
    container()->bindShared($abstract, $definition);
}

/**
 * Unbind abstract from container.
 *
 * @template T
 *
 * @param class-string<T> $abstract Abstract/Interface.
 *
 * @throws ContainerExceptionInterface
 *
 * @api
 */
function unbind(string $abstract): void
{
    container()->unbind($abstract);
}
