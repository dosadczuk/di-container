<?php
declare(strict_types=1);

namespace Container\Core;

/**
 * @internal
 */
final class Dependency {

    private bool $is_shared;

    private string $abstract;

    private string|\Closure $definition;

    private ?object $instance = null;

    public function __construct(bool $is_shared, string $abstract, null|string|\Closure $definition = null) {
        if (!interface_exists($abstract) && !class_exists($abstract)) {
            throw new \InvalidArgumentException("Invalid argument 'abstract': $abstract not found");
        }

        if (is_string($definition) && !class_exists($definition)) {
            throw new \InvalidArgumentException("Invalid argument 'definition': $definition not found");
        }

        $this->is_shared = $is_shared;
        $this->abstract = $abstract;
        $this->definition = $definition ?? $abstract;
    }

    public static function transient(string $abstract, null|string|\Closure $definition = null): self {
        return new self(false, $abstract, $definition);
    }

    public static function shared(string $abstract, null|string|\Closure $definition = null): self {
        return new self(true, $abstract, $definition);
    }

    public function isShared(): bool {
        return $this->is_shared;
    }

    public function getAbstract(): string {
        return $this->abstract;
    }

    public function getDefinition(): string|\Closure {
        return $this->definition;
    }

    public function getInstance(): ?object {
        return $this->instance;
    }

    public function isInstantiated(): bool {
        return $this->instance !== null;
    }

    public function setInstance(object $instance): void {
        $this->instance = $instance;
    }
}