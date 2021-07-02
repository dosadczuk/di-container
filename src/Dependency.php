<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Dependency {

    /**
     * Is dependency registered as shared.
     */
    private bool $is_shared;

    /**
     * Abstract.
     */
    private string $abstract;

    /**
     * Implementation of abstract.
     */
    private string|\Closure $definition;

    /**
     * Resolved instance. {@see null} means not resolved or dependency not shared.
     */
    private ?object $instance = null;

    private function __construct(bool $is_shared, string $abstract, string|\Closure $definition = null) {
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