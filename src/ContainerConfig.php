<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Dependency\Dependency;

final class ContainerConfig {

    private bool $is_sealed = false;

    /**
     * @var Dependency[]
     */
    private array $dependencies = [];

    public function getDependencies(): array {
        return $this->dependencies;
    }

    /**
     * @param Dependency[] $dependencies
     */
    public function setDependencies(array $dependencies): void {
        $this->checkSeal();
        $this->dependencies = $dependencies;
    }

    private function checkSeal(): void {
        if ($this->is_sealed) {
            throw new ContainerException('Config is closed for modifications');
        }
    }

    public function seal(): self {
        $this->is_sealed = true;

        return $this;
    }
}