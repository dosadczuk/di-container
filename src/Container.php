<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Config\Config;
use Container\Core\Config\ConfigType;
use Container\Core\Dependency\Dependency;
use Container\Core\Dependency\DependencyRegistry;

final class Container {

    private static ?self $instance = null;

    private DependencyRegistry $registry;

    private function __construct() {
        $this->registry = new DependencyRegistry();
    }

    /**
     * Get instance of Container.
     */
    public static function getInstance(): self {
        return self::$instance
            ?? self::$instance = new self();
    }

    /**
     * Loads configuration file (may override existing set up).
     *
     * @param string $config_file Name of config file.
     * @param ConfigType|null $config_type Optional config type (e.g. for file 'container.config' and XML in it).
     */
    public function load(string $config_file, ConfigType $config_type = null): self {
        $config = Config::fromFileName($config_file, $config_type);

        $this->registry->merge($config->dependencies);

        return $this;
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