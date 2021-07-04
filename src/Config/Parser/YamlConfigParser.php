<?php
declare(strict_types=1);

namespace Container\Core\Config\Parser;

use Container\Core\ContainerConfig;
use Container\Core\Dependency\Dependency;

final class YamlConfigParser implements ConfigParser {

    public function __construct(
        private string $file_name
    ) {
        if (!extension_loaded('yaml')) {
            throw new ConfigParserException('PHP ext-yaml is not loaded');
        }

        if (!is_file($file_name)) {
            throw new ConfigParserException("Config file '$file_name' not found");
        }
    }

    public function parse(): ContainerConfig {
        $config = $this->loadConfigFile();

        $container = new ContainerConfig();
        $container->dependencies = $this->parseDependencies($config);

        return $container->seal();
    }

    private function loadConfigFile(): array {
        $file_content = file_get_contents($this->file_name);
        if ($file_content === null) {
            throw new ConfigParserException("Cannot load file '$this->file_name'");
        }

        $config = yaml_parse($file_content);
        if ($config === false) {
            throw new ConfigParserException("Cannot load file '$this->file_name'");
        }

        if (!is_array($config)) {
            throw new ConfigParserException("Invalid config file format '$this->file_name'");
        }

        return $config;
    }

    /**
     * @return Dependency[]
     */
    private function parseDependencies(array $config): array {
        if (!array_key_exists('dependencies', $config)) {
            return []; // not defined => nothing to parse
        }

        $dependencies = [];
        foreach ($config['dependencies'] as $dependency) {
            try {
                $dependencies[] = new Dependency(
                    $this->getDependencyIsShared($dependency),
                    $this->getDependencyAbstract($dependency),
                    $this->getDependencyDefinition($dependency)
                );
            } catch (\InvalidArgumentException $e) {
                throw ConfigParserException::fromException($e);
            }
        }

        return $dependencies;
    }

    private function getDependencyIsShared(array $dependency): bool {
        if (!array_key_exists('shared', $dependency)) {
            return false; // default value => transient
        }

        $is_shared = filter_var($dependency['shared'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($is_shared === null) {
            return false; // default => transient
        }

        return $is_shared;
    }

    private function getDependencyAbstract(array $dependency): string {
        if (!array_key_exists('abstract', $dependency)) {
            throw new ConfigParserException("Dependency property 'abstract' is not defined");
        }

        return $dependency['abstract'];
    }

    private function getDependencyDefinition(array $dependency): ?string {
        if (!array_key_exists('definition', $dependency)) {
            return null;
        }

        return $dependency['definition'];
    }
}