<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Dependency {

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

    /**
     * Is dependency registered as shared.
     */
    private bool $is_shared;

    public function __construct(string $abstract, string|\Closure $definition = null, bool $is_shared = false) {
        $this->abstract = $abstract;
        $this->definition = $definition ?? $abstract;
        $this->is_shared = $is_shared;
    }

    public function getAbstract(): string {
        return $this->abstract;
    }

    public function getDefinition(): string|\Closure {
        return $this->definition;
    }

    public function getInstance(): object {
        return $this->instance;
    }

    public function hasInstance(): bool {
        return $this->instance !== null;
    }

    public function setInstance(object $instance): void {
        $this->instance = $instance;
    }

    public function isShared(): bool {
        return $this->is_shared;
    }
}