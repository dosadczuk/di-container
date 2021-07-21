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
     *
     * @api
     */
    public static function getInstance(): self {
        return self::$instance
            ?? self::$instance = new self();
    }

    /**
     * Load configuration file (may override current settings).
     *
     * @param string $config_file Name of config file.
     * @param ConfigType|null $config_type Optional config type (e.g. for file 'container.config' and XML in it).
     *
     * @api
     */
    public function load(string $config_file, ConfigType $config_type = null): self {
        $config = Config::fromFileName($config_file, $config_type);

        $this->registry->merge($config->dependencies);

        return $this;
    }

    /**
     * Make instance of given abstract.
     *
     * @param string $abstract Class/interface name.
     * @param array $parameters Optional parameters.
     *
     * @return object Instance of abstract.
     *
     * @api
     */
    public function make(string $abstract, array $parameters = []): object {
        try {
            return $this->registry->make($abstract, $parameters);
        } catch (\Throwable $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * Register transient dependency.
     *
     * @param string $abstract Class/interface name.
     * @param string|\Closure $definition Optional implementation or factory function.
     *
     * @api
     */
    public function register(string $abstract, string|\Closure $definition): void {
        try {
            $this->registry->add(Dependency::transient($abstract, $definition));
        } catch (\Throwable $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * Register shared dependency.
     *
     * @param string $abstract Class/interface name.
     * @param string|\Closure|null $definition Optional implementation or factory function.
     *
     * @api
     */
    public function registerShared(string $abstract, string|\Closure $definition = null): void {
        try {
            $this->registry->add(Dependency::shared($abstract, $definition));
        } catch (\Throwable $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * Unregister dependency.
     *
     * @param string $abstract Class/interface name.
     *
     * @api
     */
    public function unregister(string $abstract): void {
        try {
            $this->registry->remove($abstract);
        } catch (\Throwable $e) {
            throw ContainerException::fromThrowable($e);
        }
    }

    /**
     * Check if dependency is registered.
     *
     * @param string $abstract Class/interface name.
     *
     * @api
     */
    public function isRegistered(string $abstract): bool {
        return $this->registry->has($abstract);
    }
}