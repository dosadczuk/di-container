<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Config\ConfigCreator;
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
    public static function get(): self {
        return self::$instance
            ?? self::$instance = new self();
    }

    /**
     * Constructs Container with configuration file.
     *
     * @param string $file_name Name of config file.
     * @param ConfigType|null $type Optional config type (e.g. for file 'container.config' and XML in it).
     *
     * @return static Instance of Container with loaded config.
     */
    public static function fromConfig(string $file_name, ConfigType $type = null): self {
        return self::get()->loadConfig($file_name, $type);
    }

    /**
     * Loads configuration file (may override existing set up).
     *
     * @param string $file_name Name of config file.
     * @param ConfigType|null $type Optional config type (e.g. for file 'container.config' and XML in it).
     */
    public function loadConfig(string $file_name, ConfigType $type = null): self {
        $config = ConfigCreator::createFromFileName($file_name, $type);

        $this->registry->set($config->dependencies);

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