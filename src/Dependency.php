<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Dependency {

    /**
     * Base class/interface.
     */
    private string $abstract;

    /**
     * Implementation class.
     */
    private string $definition;

    /**
     * Resolved instance. NULL - not resolved.
     */
    private ?object $instance = null;

    /**
     * Is dependency registered shared.
     */
    private bool $is_shared;

    public function __construct(string $abstract, string $definition = null, bool $is_shared = false) {
        $this->abstract = $abstract;
        $this->definition = $definition ?? $abstract;
        $this->is_shared = $is_shared;
    }

    public function getAbstract(): string {
        return $this->abstract;
    }

    public function getDefinition(): string {
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

    public function isResolved(): bool {
        if (!$this->isShared()) {
            return false;
        }

        return $this->hasInstance();
    }
}