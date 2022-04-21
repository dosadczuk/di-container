<?php
declare(strict_types=1);

namespace Container;

use Container\Exceptions\ContainerException;

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
     * Dependency inner dependencies values.
     *
     * @var array<string, mixed>
     */
    public readonly array $arguments;

    /**
     * Instance of {@see Dependency::$abstract}. Not null only for shared dependencies.
     */
    public ?object $instance;

    private function __construct(bool $is_shared, string $abstract, null|string|\Closure $definition = null, array $arguments = [])
    {
        if (!interface_exists($abstract) && !class_exists($abstract)) {
            throw new ContainerException("'$abstract' not exists.");
        }

        if (is_string($definition) && !class_exists($definition)) {
            throw new ContainerException("'$definition' not exists.");
        }

        if (interface_exists($abstract) && $definition === null) {
            throw new ContainerException("'$abstract' cannot be instantiated, \$definition is required.");
        }

        $this->is_shared = $is_shared;
        $this->abstract = $abstract;
        $this->definition = $definition ?? $abstract;
        $this->arguments = $arguments;
        $this->instance = null;
    }

    /**
     * Create transient (not singleton) dependency.
     */
    public static function transient(string $abstract, null|string|\Closure $definition = null, array $arguments = []): self
    {
        return new self(false, $abstract, $definition, $arguments);
    }

    /**
     * Create shared (singleton) dependency.
     */
    public static function shared(string $abstract, null|string|\Closure $definition = null, array $arguments = []): self
    {
        return new self(true, $abstract, $definition, $arguments);
    }

    /**
     * Returns argument value or null if it is not provided.
     */
    public function getArgument(string $name): mixed
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * Checks if argument value is provided.
     */
    public function hasArgument(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    /**
     * Check if dependency is instantiated.
     */
    public function isInstantiated(): bool
    {
        return $this->instance !== null;
    }
}
