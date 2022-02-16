<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Exceptions\DependencyDefinitionRequiredException;
use Container\Core\Exceptions\DependencyNotExistsException;

/**
 * @internal
 */
final class Dependency
{
    /**
     * Is dependency singleton.
     */
    public readonly bool $is_shared;

    /**
     * Dependency abstract class.
     */
    public readonly string $abstract;

    /**
     * Dependency definition factory.
     */
    public readonly string|\Closure $definition;

    /**
     * Instance of {@see Dependency::$abstract}. Not null only for shared dependencies.
     */
    public ?object $instance;

    public function __construct(bool $is_shared, string $abstract, null|string|\Closure $definition = null)
    {
        if (!interface_exists($abstract) && !class_exists($abstract)) {
            throw new DependencyNotExistsException($abstract);
        }

        if (is_string($definition) && !class_exists($definition)) {
            throw new DependencyNotExistsException($definition);
        }

        if (interface_exists($abstract) && $definition === null) {
            throw new DependencyDefinitionRequiredException($abstract);
        }

        $this->is_shared = $is_shared;
        $this->abstract = $abstract;
        $this->definition = $definition ?? $abstract;
        $this->instance = null;
    }

    /**
     * Create transient (not singleton) dependency.
     */
    public static function transient(string $abstract, null|string|\Closure $definition = null): self
    {
        return new self(false, $abstract, $definition);
    }

    /**
     * Create shared (singleton) dependency.
     */
    public static function shared(string $abstract, null|string|\Closure $definition = null): self
    {
        return new self(true, $abstract, $definition);
    }

    public function isInstantiated(): bool
    {
        return $this->instance !== null;
    }
}
