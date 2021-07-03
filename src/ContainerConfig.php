<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Dependency\Dependency;

/**
 * @property Dependency[] $dependencies Predefined container dependencies.
 */
final class ContainerConfig {

    private bool $is_sealed = false;

    private array $values = [
        'dependencies' => [],
    ];

    public function seal(): self {
        $this->is_sealed = true;

        return $this;
    }

    public function __get(string $name) {
        return $this->values[$name] ?? null;
    }

    public function __set(string $name, $value): void {
        if ($this->is_sealed) {
            throw new ContainerException('Config is closed for modifications');
        }

        $this->values[$name] = $value;
    }

    public function __isset(string $name): bool {
        return array_key_exists($name, $this->values);
    }

    public function __unset(string $name): void {
        if (array_key_exists($name, $this->values)) {
            unset($this->values[$name]);
        }
    }
}