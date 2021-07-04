<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Config\ConfigType;
use Container\Core\Dependency\Dependency;
use Container\Core\Dependency\DependencyRegistry;

final class Container {

    private static ?self $instance = null;

    private DependencyRegistry $registry;

    /**
     * @param Dependency[] $dependencies
     */
    private function __construct(array $dependencies = []) {
        $this->registry = new DependencyRegistry($dependencies);
    }

    /**
     * Get instance of Container.
     */
    public static function get(): self {
        return self::$instance
            ?? self::$instance = new self();
    }

    /**
     * Constructs Container with configuration file.
     *
     * @param string $file_name Name of file with dependencies.
     * @param ConfigType|null $type Optional config type (e.g. for file 'container.config' and XML in it).
     *
     * @return static Instance of Container with loaded dependencies.
     */
    public static function fromConfig(string $file_name, ConfigType $type = null): self {
        if (($type ??= ConfigType::fromFileName($file_name)) === null) {
            throw new ContainerException("Cannot determine config type for file '$file_name'");
        }

        $config = $type->getParser($file_name)->parse();

        return new self($config->dependencies);
    }

    /**
     * Make instance of given abstract.
     *
     * @param string $abstract Name of the abstract.
     * @param array $parameters Optional parameters.
     *
     * @return object Instance of abstract.
     */
    public function make(string $abstract, array $parameters = []): object {
        return $this->registry->make($abstract, $parameters);
    }

    /**
     * Register dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|\Closure $definition Optional implementation.
     */
    public function register(string $abstract, string|\Closure $definition): void {
        $this->registry->add(Dependency::transient($abstract, $definition));
    }

    /**
     * Register shared dependency.
     *
     * @param string $abstract Base class/interface.
     * @param string|\Closure|null $definition Optional implementation.
     */
    public function registerShared(string $abstract, string|\Closure $definition = null): void {
        $this->registry->add(Dependency::shared($abstract, $definition));
    }

    /**
     * Unregister dependency.
     *
     * @param string $abstract Base class/interface.
     */
    public function unregister(string $abstract): void {
        $this->registry->remove($abstract);
    }

    /**
     * Check if dependency is registered.
     *
     * @param string $abstract Base class/interface.
     */
    public function isRegistered(string $abstract): bool {
        return $this->registry->has($abstract);
    }
}