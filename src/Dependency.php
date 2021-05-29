<?php
declare(strict_types=1);

namespace Foundation\Container;

final class Dependency implements \JsonSerializable {

    /**
     * Base class/interface.
     */
    private string $abstract;

    /**
     * Concrete implementation.
     */
    private string $concrete;

    /**
     * Resolved instance. NULL - not resolved.
     */
    private ?object $instance = null;

    /**
     * Is dependency registered as singleton.
     */
    private bool $is_singleton;

    public function __construct(string $abstract, string $concrete = null, bool $is_singleton = false) {
        $this->abstract = $abstract;
        $this->concrete = $concrete ?? $abstract;
        $this->is_singleton = $is_singleton;
    }

    public function getAbstract(): string {
        return $this->abstract;
    }

    public function getConcrete(): string {
        return $this->concrete;
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

    public function isSingleton(): bool {
        return $this->is_singleton;
    }

    public function isResolved(): bool {
        if ($this->isSingleton()) {
            return false;
        }

        return $this->hasInstance();
    }

    public function jsonSerialize(): array {
        return [
            'abstract'     => $this->getAbstract(),
            'concrete'     => $this->getConcrete(),
            'is_singleton' => $this->isSingleton(),
        ];
    }
}